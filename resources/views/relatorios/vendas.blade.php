@extends('layouts.app')

@section('title', 'Relatório de Vendas')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h2">Relatório de Vendas</h1>
        <div>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="bi bi-printer"></i> Imprimir
            </button>
            <button onclick="exportToExcel()" class="btn btn-success">
                <i class="bi bi-file-excel"></i> Exportar Excel
            </button>
        </div>
    </div>
@endsection

@section('content')
<!-- Filtros -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 fw-bold text-primary">Filtros</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('relatorios.vendas') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Data Início</label>
                <input type="date" name="data_inicio" class="form-control" value="{{ $request->data_inicio }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Data Fim</label>
                <input type="date" name="data_fim" class="form-control" value="{{ $request->data_fim }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="todos">Todos</option>
                    <option value="aberto" {{ $request->status == 'aberto' ? 'selected' : '' }}>Aberto</option>
                    <option value="parcial" {{ $request->status == 'parcial' ? 'selected' : '' }}>Parcial</option>
                    <option value="pago" {{ $request->status == 'pago' ? 'selected' : '' }}>Pago</option>
                    <option value="cancelado" {{ $request->status == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Cliente</label>
                <select name="cliente_id" class="form-control">
                    <option value="">Todos</option>
                    @foreach($clientes as $cliente)
                        <option value="{{ $cliente->id }}" {{ $request->cliente_id == $cliente->id ? 'selected' : '' }}>
                            {{ $cliente->nome }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Filtrar
                </button>
                <a href="{{ route('relatorios.vendas') }}" class="btn btn-secondary">
                    <i class="bi bi-eraser"></i> Limpar Filtros
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Cards de Resumo -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6 class="card-title">Total de Vendas</h6>
                <h3 class="card-text">R$ {{ number_format($totais['valor_total'], 2, ',', '.') }}</h3>
                <small>{{ $totais['quantidade'] }} vendas</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="card-title">Total Recebido</h6>
                <h3 class="card-text">R$ {{ number_format($totais['total_pago'], 2, ',', '.') }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h6 class="card-title">Total a Receber</h6>
                <h3 class="card-text">R$ {{ number_format($totais['total_debito'], 2, ',', '.') }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h6 class="card-title">Ticket Médio</h6>
                <h3 class="card-text">
                    R$ {{ number_format($totais['quantidade'] > 0 ? $totais['valor_total'] / $totais['quantidade'] : 0, 2, ',', '.') }}
                </h3>
            </div>
        </div>
    </div>
</div>

<!-- Lista de Vendas -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 fw-bold text-primary">Lista de Vendas</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="vendasTable">
                <thead>
                    <tr>
                        <th>Nº Venda</th>
                        <th>Data</th>
                        <th>Cliente</th>
                        <th>Valor Total</th>
                        <th>Desconto</th>
                        <th>Valor Final</th>
                        <th>Pago</th>
                        <th>Saldo</th>
                        <th>Status</th>
                        <th>Vendedor</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vendas as $venda)
                    <tr>
                        <td><strong>#{{ $venda->id }}</strong><br>
                            <small class="text-muted">{{ $venda->numero_venda ?? 'VENDA-' . $venda->id }}</small>
                        </td>
                        <td>{{ $venda->data_venda->format('d/m/Y') }}<br>
                            <small>{{ $venda->created_at->format('H:i') }}</small>
                        </td>
                        <td>
                            {{ $venda->cliente->nome }}
                            @if($venda->cliente->telefone)
                                <br><small class="text-muted">{{ $venda->cliente->telefone }}</small>
                            @endif
                        </td>
                        <td>R$ {{ number_format($venda->valor_total, 2, ',', '.') }}</td>
                        <td class="text-danger">- R$ {{ number_format($venda->desconto, 2, ',', '.') }}</td>
                        <td class="fw-bold">R$ {{ number_format($venda->valor_final, 2, ',', '.') }}</td>
                        <td class="text-success">R$ {{ number_format($venda->total_pago, 2, ',', '.') }}</td>
                        <td class="text-warning fw-bold">R$ {{ number_format($venda->saldo, 2, ',', '.') }}</td>
                        <td>
                            @php
                                $statusClass = [
                                    'aberto' => 'warning',
                                    'parcial' => 'info',
                                    'pago' => 'success',
                                    'cancelado' => 'danger'
                                ][$venda->status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $statusClass }}">
                                {{ ucfirst($venda->status) }}
                            </span>
                            @if($venda->saldo > 0 && $venda->status != 'cancelado')
                                <div class="progress mt-1" style="height: 4px;">
                                    <div class="progress-bar bg-success" style="width: {{ $venda->percentual_pago }}%"></div>
                                </div>
                            @endif
                        </td>
                        <td>{{ $venda->user->name ?? 'Sistema' }}</td>
                        <td>
                            <a href="{{ route('vendas.show', $venda) }}" class="btn btn-sm btn-info" title="Ver">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if($venda->saldo > 0)
                                <a href="{{ route('pagamentos.create', $venda) }}" class="btn btn-sm btn-success" title="Registrar Pagamento">
                                    <i class="bi bi-cash"></i>
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center">Nenhuma venda encontrada</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" class="text-end">Totais:</th>
                        <th>R$ {{ number_format($vendas->sum('valor_total'), 2, ',', '.') }}</th>
                        <th>R$ {{ number_format($vendas->sum('desconto'), 2, ',', '.') }}</th>
                        <th>R$ {{ number_format($vendas->sum('valor_final'), 2, ',', '.') }}</th>
                        <th>R$ {{ number_format($vendas->sum('total_pago'), 2, ',', '.') }}</th>
                        <th>R$ {{ number_format($vendas->sum('saldo'), 2, ',', '.') }}</th>
                        <th colspan="2"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <div class="mt-3">
            {{ $vendas->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#vendasTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json'
            },
            paging: false,
            searching: false,
            info: false,
            ordering: true
        });
    });
    
    function exportToExcel() {
        // Pegar os filtros atuais
        const params = new URLSearchParams(window.location.search);
        window.location.href = "{{ route('relatorios.vendas') }}/export?" + params.toString();
    }
</script>

<style>
    @media print {
        .btn, .navbar, .sidebar, .card-header .btn, form, .dataTables_filter, .dataTables_length, .dataTables_paginate {
            display: none !important;
        }
        .sidebar, .col-md-2 {
            display: none !important;
        }
        main {
            margin-left: 0 !important;
            width: 100% !important;
        }
        .card {
            break-inside: avoid;
        }
        tfoot {
            display: table-footer-group;
        }
    }
</style>
@endpush