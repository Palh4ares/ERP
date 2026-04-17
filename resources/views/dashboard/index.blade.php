@extends('layouts.app')

@section('title', 'Dashboard')

@section('header')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0">Dashboard</h2>
        <small class="text-muted">Visão geral do sistema</small>
    </div>

    <div class="text-end">
        <small class="text-muted">Bem-vindo,</small><br>
        <strong>{{ Auth::user()->name }}</strong>
    </div>
</div>
@endsection

@section('content')

{{-- CARDS --}}
<div class="row g-3 mb-4">

    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body d-flex align-items-center">
                <i class="bi bi-cart-fill fs-2 text-primary me-3"></i>
                <div>
                    <small class="text-muted">Total de Vendas</small>
                    <h4 class="mb-0 fw-bold">R$ {{ number_format($totalVendas, 2, ',', '.') }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body d-flex align-items-center">
                <i class="bi bi-cash-stack fs-2 text-success me-3"></i>
                <div>
                    <small class="text-muted">Total Recebido</small>
                    <h4 class="mb-0 fw-bold">R$ {{ number_format($totalRecebido, 2, ',', '.') }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body d-flex align-items-center">
                <i class="bi bi-exclamation-circle fs-2 text-warning me-3"></i>
                <div>
                    <small class="text-muted">A Receber</small>
                    <h4 class="mb-0 fw-bold text-danger">R$ {{ number_format($totalAReceber, 2, ',', '.') }}</h4>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ÚLTIMAS VENDAS --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-0">
        <h5 class="mb-0 fw-semibold">Últimas Vendas</h5>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle">

                <thead class="table-light">
                    <tr>
                        <th>Cliente</th>
                        <th>Valor</th>
                        <th>Pago</th>
                        <th>Saldo</th>
                        <th>Status</th>
                        <th>Data</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($vendasRecentes as $venda)
                    <tr>
                        <td>{{ $venda->cliente->nome ?? 'N/A' }}</td>

                        <td>R$ {{ number_format($venda->valor_final, 2, ',', '.') }}</td>

                        <td class="text-success">
                            R$ {{ number_format($venda->total_pago, 2, ',', '.') }}
                        </td>

                        <td class="{{ $venda->saldo > 0 ? 'text-danger fw-bold' : 'text-success' }}">
                            R$ {{ number_format($venda->saldo, 2, ',', '.') }}
                        </td>

                        <td>
                            <span class="badge bg-{{ $venda->status == 'pago' ? 'success' : 'warning' }}">
                                {{ ucfirst($venda->status) }}
                            </span>
                        </td>

                        <td>{{ $venda->created_at->format('d/m/Y') }}</td>

                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('vendas.show', $venda) }}" class="btn btn-light">
                                    <i class="bi bi-eye"></i>
                                </a>

                                @if($venda->saldo > 0)
                                <a href="{{ route('pagamentos.create', $venda) }}" class="btn btn-success">
                                    <i class="bi bi-cash"></i>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            Nenhuma venda cadastrada
                        </td>
                    </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>
</div>

{{-- AÇÕES RÁPIDAS --}}
<div class="row g-3 mb-4">

    <div class="col-md-6">
        <a href="{{ route('vendas.index') }}" class="card shadow-sm border-0 text-decoration-none">
            <div class="card-body d-flex align-items-center">
                <i class="bi bi-cart fs-3 me-3 text-primary"></i>
                <strong>Ver Vendas</strong>
            </div>
        </a>
    </div>

    <div class="col-md-6">
        <a href="{{ route('clientes.index') }}" class="card shadow-sm border-0 text-decoration-none">
            <div class="card-body d-flex align-items-center">
                <i class="bi bi-people fs-3 me-3 text-info"></i>
                <strong>Ver Clientes</strong>
            </div>
        </a>
    </div>

</div>

{{-- PAGAMENTOS PENDENTES --}}
@php
    $vendasPendentes = $vendasRecentes->filter(fn($venda) => $venda->saldo > 0)->take(5);
@endphp

@if($vendasPendentes->count() > 0)
<div class="card shadow-sm border-0">
    <div class="card-header bg-warning text-white">
        <i class="bi bi-exclamation-triangle"></i> Pagamentos Pendentes
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">

                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Saldo</th>
                        <th>Data</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($vendasPendentes as $venda)
                    <tr>
                        <td>{{ $venda->cliente->nome }}</td>

                        <td class="text-danger fw-bold">
                            R$ {{ number_format($venda->saldo, 2, ',', '.') }}
                        </td>

                        <td>{{ $venda->created_at->format('d/m/Y') }}</td>

                        <td class="text-end">
                            <a href="{{ route('pagamentos.create', $venda) }}" class="btn btn-success btn-sm">
                                <i class="bi bi-cash"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
// Atualiza automático (opcional)
setTimeout(() => {
    location.reload();
}, 30000);
</script>
@endpush