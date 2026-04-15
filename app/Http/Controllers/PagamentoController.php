<?php

namespace App\Http\Controllers;

use App\Models\Venda;
use App\Models\Pagamento;
use Illuminate\Http\Request;

class PagamentoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create(Venda $venda)
    {
        $venda->load('cliente');
        return view('pagamentos.create', compact('venda'));
    }

    public function store(Request $request, Venda $venda)
    {
        $request->validate([
            'valor' => 'required|numeric|min:0.01|max:' . $venda->saldo,
            'data_pagamento' => 'required|date',
            'observacao' => 'nullable|max:255'
        ]);

        Pagamento::create([
            'venda_id' => $venda->id,
            'user_id' => auth()->id(),
            'valor' => $request->valor,
            'data_pagamento' => $request->data_pagamento,
            'observacao' => $request->observacao
        ]);

        return redirect()->route('vendas.show', $venda)
            ->with('success', 'Pagamento registrado com sucesso!');
    }

    public function destroy(Pagamento $pagamento)
    {
        $venda = $pagamento->venda;
        $pagamento->delete();
        
        return redirect()->route('vendas.show', $venda)
            ->with('success', 'Pagamento removido com sucesso!');
    }
}