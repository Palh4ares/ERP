<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Venda;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VendaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $vendas = Venda::with('cliente')->orderBy('created_at', 'desc')->get();
        return view('vendas.index', compact('vendas'));
    }

    public function create(Request $request)
    {
        $clientes = Cliente::orderBy('nome')->get();
        $clienteSelecionado = null;
        
        // Se veio um cliente_id na URL, seleciona ele automaticamente
        if ($request->has('cliente_id')) {
            $clienteSelecionado = Cliente::find($request->cliente_id);
        }
        
        return view('vendas.create', compact('clientes', 'clienteSelecionado'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'valor_total' => 'required|numeric|min:0.01',
            'data_venda' => 'required|date'
        ]);

        // Gerar número da venda
        $ultimaVenda = Venda::latest()->first();
        $numero = $ultimaVenda ? $ultimaVenda->id + 1 : 1;
        $numeroVenda = 'VENDA-' . date('Ymd') . '-' . str_pad($numero, 4, '0', STR_PAD_LEFT);

        $venda = Venda::create([
            'cliente_id' => $request->cliente_id,
            'user_id' => auth()->id(),
            'numero_venda' => $numeroVenda,
            'valor_total' => $request->valor_total,
            'desconto' => $request->desconto ?? 0,
            'valor_final' => $request->valor_total - ($request->desconto ?? 0),
            'data_venda' => $request->data_venda,
            'status' => 'aberto'
        ]);

        return redirect()->route('vendas.index')
            ->with('success', 'Venda cadastrada com sucesso! Nº: ' . $numeroVenda);
    }

    public function show(Venda $venda)
    {
        $venda->load(['cliente', 'pagamentos.user']);
        return view('vendas.show', compact('venda'));
    }

    public function edit(Venda $venda)
    {
        $clientes = Cliente::orderBy('nome')->get();
        return view('vendas.edit', compact('venda', 'clientes'));
    }

    public function update(Request $request, Venda $venda)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'valor_total' => 'required|numeric|min:0.01',
            'data_venda' => 'required|date'
        ]);

        $venda->update([
            'cliente_id' => $request->cliente_id,
            'valor_total' => $request->valor_total,
            'desconto' => $request->desconto ?? 0,
            'valor_final' => $request->valor_total - ($request->desconto ?? 0),
            'data_venda' => $request->data_venda
        ]);
        
        $venda->atualizarStatus();
        
        return redirect()->route('vendas.index')
            ->with('success', 'Venda atualizada com sucesso!');
    }

    public function destroy(Venda $venda)
    {
        $venda->delete();
        
        return redirect()->route('vendas.index')
            ->with('success', 'Venda removida com sucesso!');
    }
}