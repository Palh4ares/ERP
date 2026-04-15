<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Venda;
use App\Models\Pagamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RelatorioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function vendas(Request $request)
    {
        $query = Venda::with(['cliente', 'user']);
        
        // Aplicar filtros
        if ($request->filled('data_inicio')) {
            $query->whereDate('data_venda', '>=', $request->data_inicio);
        }
        if ($request->filled('data_fim')) {
            $query->whereDate('data_venda', '<=', $request->data_fim);
        }
        if ($request->filled('status') && $request->status != 'todos') {
            $query->where('status', $request->status);
        }
        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->cliente_id);
        }
        if ($request->filled('busca')) {
            $query->whereHas('cliente', function($q) use ($request) {
                $q->where('nome', 'like', '%' . $request->busca . '%');
            });
        }
        
        // Clonar query para totais antes da paginação
        $queryTotal = clone $query;
        
        $vendas = $query->orderBy('data_venda', 'desc')->paginate(20);
        
        // Calcular totais
        $totalValorFinal = $queryTotal->sum('valor_final');
        $totalQuantidade = $queryTotal->count();
        $vendaIds = $queryTotal->pluck('id');
        
        $totalPago = Pagamento::whereIn('venda_id', $vendaIds)->sum('valor');
        $totalDebito = $totalValorFinal - $totalPago;
        
        $totais = [
            'valor_total' => $totalValorFinal,
            'quantidade' => $totalQuantidade,
            'total_pago' => $totalPago,
            'total_debito' => $totalDebito
        ];
        
        $clientes = Cliente::orderBy('nome')->get();
        
        return view('relatorios.vendas', compact('vendas', 'clientes', 'totais', 'request'));
    }
    
    public function clientes(Request $request)
    {
        $query = Cliente::query();
        
        // Aplicar filtros
        if ($request->filled('status') && $request->status != 'todos') {
            $query->where('status', $request->status);
        }
        if ($request->filled('busca')) {
            $query->where(function($q) use ($request) {
                $q->where('nome', 'like', '%' . $request->busca . '%')
                  ->orWhere('telefone', 'like', '%' . $request->busca . '%')
                  ->orWhere('cpf_cnpj', 'like', '%' . $request->busca . '%');
            });
        }
        
        $clientes = $query->orderBy('nome')->paginate(20);
        
        // Calcular total de compras para cada cliente
        foreach($clientes as $cliente) {
            $cliente->total_compras = Venda::where('cliente_id', $cliente->id)->sum('valor_final');
            $cliente->total_pago = Pagamento::whereHas('venda', function($q) use ($cliente) {
                $q->where('cliente_id', $cliente->id);
            })->sum('valor');
            $cliente->total_devido = $cliente->total_compras - $cliente->total_pago;
        }
        
        $totalGeral = $clientes->sum('total_compras');
        $totalDevidoGeral = $clientes->sum('total_devido');
        
        return view('relatorios.clientes', compact('clientes', 'totalGeral', 'totalDevidoGeral', 'request'));
    }
    
    public function pagamentos(Request $request)
    {
        $query = Pagamento::with(['venda.cliente', 'user']);
        
        // Aplicar filtros
        if ($request->filled('data_inicio')) {
            $query->whereDate('data_pagamento', '>=', $request->data_inicio);
        }
        if ($request->filled('data_fim')) {
            $query->whereDate('data_pagamento', '<=', $request->data_fim);
        }
        if ($request->filled('forma_pagamento') && $request->forma_pagamento != 'todos') {
            $query->where('forma_pagamento', $request->forma_pagamento);
        }
        if ($request->filled('cliente_id')) {
            $query->whereHas('venda', function($q) use ($request) {
                $q->where('cliente_id', $request->cliente_id);
            });
        }
        
        $pagamentos = $query->orderBy('data_pagamento', 'desc')->paginate(20);
        
        // Clonar query para totais
        $queryTotal = clone $query;
        
        // Totais gerais
        $totalValor = $queryTotal->sum('valor');
        $totalQuantidade = $queryTotal->count();
        
        // Totais por forma de pagamento
        $totaisPorForma = Pagamento::selectRaw('forma_pagamento, SUM(valor) as total, COUNT(*) as quantidade')
            ->when($request->filled('data_inicio'), function($q) use ($request) {
                return $q->whereDate('data_pagamento', '>=', $request->data_inicio);
            })
            ->when($request->filled('data_fim'), function($q) use ($request) {
                return $q->whereDate('data_pagamento', '<=', $request->data_fim);
            })
            ->when($request->filled('cliente_id'), function($q) use ($request) {
                return $q->whereHas('venda', function($sub) use ($request) {
                    $sub->where('cliente_id', $request->cliente_id);
                });
            })
            ->groupBy('forma_pagamento')
            ->get();
        
        $totais = [
            'valor_total' => $totalValor,
            'quantidade' => $totalQuantidade,
            'por_forma' => $totaisPorForma
        ];
        
        $clientes = Cliente::orderBy('nome')->get();
        
        return view('relatorios.pagamentos', compact('pagamentos', 'totais', 'request', 'clientes'));
    }
    
    // Método para exportar vendas para Excel/CSV
    public function exportVendas(Request $request)
    {
        $query = Venda::with(['cliente', 'user']);
        
        if ($request->filled('data_inicio')) {
            $query->whereDate('data_venda', '>=', $request->data_inicio);
        }
        if ($request->filled('data_fim')) {
            $query->whereDate('data_venda', '<=', $request->data_fim);
        }
        if ($request->filled('status') && $request->status != 'todos') {
            $query->where('status', $request->status);
        }
        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->cliente_id);
        }
        
        $vendas = $query->orderBy('data_venda', 'desc')->get();
        
        $filename = 'relatorio_vendas_' . date('Ymd_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($vendas) {
            $file = fopen('php://output', 'w');
            
            // Adicionar BOM para UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Cabeçalhos
            fputcsv($file, [
                'ID', 'Número', 'Data', 'Cliente', 'Telefone', 
                'Valor Total', 'Desconto', 'Valor Final', 'Pago', 'Saldo', 
                'Status', 'Vendedor'
            ], ';');
            
            // Dados
            foreach ($vendas as $venda) {
                fputcsv($file, [
                    $venda->id,
                    $venda->numero_venda ?? 'VENDA-' . $venda->id,
                    $venda->data_venda->format('d/m/Y'),
                    $venda->cliente->nome ?? 'N/A',
                    $venda->cliente->telefone ?? 'N/A',
                    number_format($venda->valor_total, 2, ',', '.'),
                    number_format($venda->desconto, 2, ',', '.'),
                    number_format($venda->valor_final, 2, ',', '.'),
                    number_format($venda->total_pago, 2, ',', '.'),
                    number_format($venda->saldo, 2, ',', '.'),
                    $venda->status,
                    $venda->user->name ?? 'Sistema'
                ], ';');
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    // Método para exportar clientes para Excel/CSV
    public function exportClientes(Request $request)
    {
        $query = Cliente::query();
        
        if ($request->filled('status') && $request->status != 'todos') {
            $query->where('status', $request->status);
        }
        if ($request->filled('busca')) {
            $query->where('nome', 'like', '%' . $request->busca . '%');
        }
        
        $clientes = $query->orderBy('nome')->get();
        
        foreach($clientes as $cliente) {
            $cliente->total_compras = Venda::where('cliente_id', $cliente->id)->sum('valor_final');
            $cliente->total_devido = $cliente->total_compras - Pagamento::whereHas('venda', function($q) use ($cliente) {
                $q->where('cliente_id', $cliente->id);
            })->sum('valor');
        }
        
        $filename = 'relatorio_clientes_' . date('Ymd_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($clientes) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, [
                'ID', 'Nome', 'Telefone', 'Email', 'CPF/CNPJ', 
                'Status', 'Total Compras', 'Total Devido', 'Data Cadastro'
            ], ';');
            
            foreach ($clientes as $cliente) {
                fputcsv($file, [
                    $cliente->id,
                    $cliente->nome,
                    $cliente->telefone,
                    $cliente->email ?? '-',
                    $cliente->cpf_cnpj ?? '-',
                    $cliente->status,
                    number_format($cliente->total_compras, 2, ',', '.'),
                    number_format($cliente->total_devido, 2, ',', '.'),
                    $cliente->created_at->format('d/m/Y')
                ], ';');
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}