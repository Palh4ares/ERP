@extends('layouts.app')

@section('title', 'Registrar Pagamento')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h2">Registrar Pagamento</h1>
        <a href="{{ route('vendas.show', $venda) }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
    </div>
@endsection

@section('content')
<div class="card shadow">
    <div class="card-body">
        <div class="alert alert-info">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Cliente:</strong> {{ $venda->cliente->nome }}</p>
                    <p><strong>Valor Total:</strong> R$ {{ number_format($venda->valor_total, 2, ',', '.') }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Total Pago:</strong> R$ {{ number_format($venda->total_pago, 2, ',', '.') }}</p>
                    <p><strong>Saldo Restante:</strong> 
                        <span class="text-danger fw-bold">R$ {{ number_format($venda->saldo, 2, ',', '.') }}</span>
                    </p>
                </div>
            </div>
        </div>
        
        <form action="{{ route('pagamentos.store', $venda) }}" method="POST">
            @csrf
            
            <div class="mb-3">
    <label class="form-label">Valor do Pagamento *</label>

    <input 
        type="number" 
        step="0.01"
        class="form-control @error('valor') is-invalid @enderror" 
        name="valor"
        value="{{ old('valor') }}"
        max="{{ $venda->saldo }}"
        min="0.01"
        placeholder="Digite o valor pago"
        required
    >

    @error('valor')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror    
</div>
            
            <div class="mb-3">
                <label class="form-label">Data do Pagamento *</label>
                <input type="date" class="form-control @error('data_pagamento') is-invalid @enderror" 
                       name="data_pagamento" value="{{ old('data_pagamento', date('Y-m-d')) }}" required>
                @error('data_pagamento')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Registrar Pagamento
                </button>
                <a href="{{ route('vendas.show', $venda) }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection