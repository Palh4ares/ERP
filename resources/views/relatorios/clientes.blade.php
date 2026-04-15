@extends('layouts.app')

@section('title', 'Relatório de Clientes')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h2">Relatório de Clientes</h1>
        <button onclick="window.print()" class="btn btn-primary">
            <i class="bi bi-printer"></i> Imprimir
        </button>
    </div>
@endsection

@section('content')
<!-- Filtros -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 fw-bold text-primary">Filtros</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('relatorios.clientes') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="todos">Todos</option>
                    <option value="ativo" {{ $request->status == 'ativo' ? 'selected' : '' }}>Ativos</option>
                    <option value="inativo" {{ $request->status == 'inativo' ? 'selected' : '' }}>Inativos</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Buscar por Nome</label>
                <input type="text" name="busca" class="form-control" placeholder="Digite o nome..." value="{{ $request->busca }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">&nbsp;</label>
                <div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Filtrar
                    </button>
                    <a href="{{ route('relatorios.clientes') }}" class="btn btn-secondary">
                        <i class="bi bi-eraser"></i> Limpar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Cards de Resumo -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6 class="card-title">Total de Clientes</h6>
                <h3 class="card-text">{{ $clientes->total() }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="card-title">Total em Compras</h6>
                <h3 class="card-text">R$ {{ number_format($totalGeral, 2, ',', '.') }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h6 class="card-title">Ticket Médio</h6>
                <h3 class="card-text">
                    R$ {{ number_format($clientes->total() > 0 ? $totalGeral / $clientes->total() : 0, 2, ',', '.') }}
                </h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h6 class="card-title">Clientes com Dívida</h6>
                <h3 class="card-text">
                    {{ $clientes->filter(function($c) { return $c->total_devido > 0; })->count() }}
                </h3>
            </div>
        </div>
    </div>
</div>

<!-- Lista de Clientes -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 fw-bold text-primary">Lista de Clientes</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="clientesTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Telefone</th>
                        <th>Email</th>
                        <th>Total Compras</th>
                        <th>Total Devido</th>
                        <th>Status</th>
                        <th>Cadastro</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clientes as $cliente)
                    <tr>
                        <td>{{ $cliente->id }}</td>
                        <td>
                            <strong>{{ $cliente->nome }}</strong>
                            @if($cliente->cpf_cnpj)
                                <br><small class="text-muted">{{ $cliente->cpf_cnpj }}</small>
                            @endif
                        </td>
                        <td>{{ $cliente->telefone }}@if($cliente->celular) / {{ $cliente->celular }}@endif</td>
                        <td>{{ $cliente->email ?? '-' }}</td>
                        <td class="text-success fw-bold">R$ {{ number_format($cliente->vendas_sum_valor_final ?? 0, 2, ',', '.') }}</td>
                        <td class="text-danger fw-bold">R$ {{ number_format($cliente->total_devido ?? 0, 2, ',', '.') }}</td>
                        <td>
                            <span class="badge bg-{{ $cliente->status == 'ativo' ? 'success' : 'danger' }}">
                                {{ ucfirst($cliente->status) }}
                            </span>
                        </td>
                        <td>{{ $cliente->created_at->format('d/m/Y') }}</td>
                        <td>
                            <a href="{{ route('clientes.show', $cliente) }}" class="btn btn-sm btn-info" title="Ver">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('vendas.create', ['cliente_id' => $cliente->id]) }}" class="btn btn-sm btn-success" title="Nova Venda">
                                <i class="bi bi-cart-plus"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center">Nenhum cliente encontrado</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            {{ $clientes->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#clientesTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json'
            },
            paging: false,
            searching: false,
            info: false
        });
    });
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
    }
</style>
@endpush