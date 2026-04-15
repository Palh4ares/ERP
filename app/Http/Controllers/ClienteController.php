<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Pagamento;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $clientes = Cliente::orderBy('nome')->get();
        
        // Calcular total de compras para cada cliente
        foreach($clientes as $cliente) {
            $cliente->total_compras = $cliente->vendas()->sum('valor_final');
            $cliente->total_pago = Pagamento::whereHas('venda', function($q) use ($cliente) {
                $q->where('cliente_id', $cliente->id);
            })->sum('valor');
            $cliente->total_devido = $cliente->total_compras - $cliente->total_pago;
        }
        
        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|max:100',
            'telefone' => 'required|max:20'
        ]);

        Cliente::create($request->all());
        
        return redirect()->route('clientes.index')
            ->with('success', 'Cliente cadastrado com sucesso!');
    }

    public function show(Cliente $cliente)
    {
        // Calcular totais do cliente
        $cliente->total_compras = $cliente->vendas()->sum('valor_final');
        $cliente->total_pago = Pagamento::whereHas('venda', function($q) use ($cliente) {
            $q->where('cliente_id', $cliente->id);
        })->sum('valor');
        $cliente->total_devido = $cliente->total_compras - $cliente->total_pago;
        
        // Carregar vendas com pagamentos
        $cliente->load(['vendas' => function($q) {
            $q->orderBy('created_at', 'desc');
        }]);
        
        return view('clientes.show', compact('cliente'));
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $request->validate([
            'nome' => 'required|max:100',
            'telefone' => 'required|max:20'
        ]);

        $cliente->update($request->all());
        
        return redirect()->route('clientes.index')
            ->with('success', 'Cliente atualizado com sucesso!');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();
        
        return redirect()->route('clientes.index')
            ->with('success', 'Cliente removido com sucesso!');
    }
}