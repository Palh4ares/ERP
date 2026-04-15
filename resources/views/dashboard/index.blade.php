@extends('layouts.app')

@section('title', 'Dashboard')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h2">Dashboard</h1>
        <span>Bem-vindo, {{ Auth::user()->name }}!</span>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title">Total de Vendas</h5>
                <h2 class="card-text">R$ {{ number_format($totalVendas, 2, ',', '.') }}</h2>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5 class="card-title">Total Recebido</h5>
                <h2 class="card-text">R$ {{ number_format($totalRecebido, 2, ',', '.') }}</h2>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h5 class="card-title">Total a Receber</h5>
                <h2 class="card-text">R$ {{ number_format($totalAReceber, 2, ',', '.') }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Últimas Vendas</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Valor</th>
                                <th>Pago</th>
                                <th>Saldo</th>
                                <th>Status</th>
                                <th>Data</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vendasRecentes as $venda)
                            <tr>
                                <td>{{ $venda->cliente->nome ?? 'N/A' }}</td>
                                <td>R$ {{ number_format($venda->valor_final, 2, ',', '.') }}</td>
                                <td>R$ {{ number_format($venda->total_pago, 2, ',', '.') }}</td>
                                <td class="{{ $venda->saldo > 0 ? 'text-danger fw-bold' : 'text-success' }}">
                                    R$ {{ number_format($venda->saldo, 2, ',', '.') }}
                                </td>
                                <td>
                                    <span class="badge bg-{{ $venda->status == 'pago' ? 'success' : 'warning' }}">
                                        {{ ucfirst($venda->status) }}
                                    </span>
                                </td>
                                <td>{{ $venda->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('vendas.show', $venda) }}" class="btn btn-info" title="Ver Detalhes">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if($venda->saldo > 0)
                                            <a href="{{ route('pagamentos.create', $venda) }}" class="btn btn-success" title="Registrar Pagamento">
                                                <i class="bi bi-cash"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">Nenhuma venda cadastrada</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cards Rápidos para Registrar Pagamento -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card bg-light">
            <div class="card-header">
                <h5><i class="bi bi-cash-stack"></i> Ações Rápidas</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="d-grid gap-2">
                            <a href="{{ route('vendas.index') }}" class="btn btn-primary btn-lg">
                                <i class="bi bi-cart"></i> Ver Todas as Vendas
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-grid gap-2">
                            <a href="{{ route('clientes.index') }}" class="btn btn-info btn-lg">
                                <i class="bi bi-people"></i> Ver Clientes
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Vendas com Pagamento Pendente (Destaque) -->
@php
    $vendasPendentes = $vendasRecentes->filter(function($venda) {
        return $venda->saldo > 0;
    })->take(5);
@endphp

@if($vendasPendentes->count() > 0)
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card border-warning">
            <div class="card-header bg-warning text-white">
                <h5><i class="bi bi-exclamation-triangle"></i> Pagamentos Pendentes</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Valor Total</th>
                                <th>Já Pago</th>
                                <th>Saldo Devedor</th>
                                <th>Data da Venda</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vendasPendentes as $venda)
                            <tr>
                                <td>{{ $venda->cliente->nome ?? 'N/A' }}</td>
                                <td>R$ {{ number_format($venda->valor_final, 2, ',', '.') }}</td>
                                <td>R$ {{ number_format($venda->total_pago, 2, ',', '.') }}</td>
                                <td class="text-danger fw-bold">R$ {{ number_format($venda->saldo, 2, ',', '.') }}</td>
                                <td>{{ $venda->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <a href="{{ route('pagamentos.create', $venda) }}" class="btn btn-sm btn-success">
                                        <i class="bi bi-cash"></i> Registrar Pagamento
                                    </a>
                                    <a href="{{ route('vendas.show', $venda) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i> Detalhes
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
    // Atualizar a cada 30 segundos (opcional)
    setTimeout(function() {
        location.reload();
    }, 30000);
</script>
@endpush