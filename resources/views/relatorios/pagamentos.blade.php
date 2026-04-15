@extends('layouts.app')

@section('title', 'Relatório de Pagamentos')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h2">Relatório de Pagamentos</h1>
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
        <form method="GET" action="{{ route('relatorios.pagamentos') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Data Início</label>
                <input type="date" name="data_inicio" class="form-control" value="{{ $request->data_inicio }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Data Fim</label>
                <input type="date" name="data_fim" class="form-control" value="{{ $request->data_fim }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Forma de Pagamento</label>
                <select name="forma_pagamento" class="form-control">
                    <option value="todos">Todos</option>
                    <option value="dinheiro" {{ $request->forma_pagamento == 'dinheiro' ? 'selected' : '' }}>Dinheiro</option>
                    <option value="cartao_credito" {{ $request->forma_pagamento == 'cartao_credito' ? 'selected' : '' }}>Cartão Crédito</option>
                    <option value="cartao_debito" {{ $request->forma_pagamento == 'cartao_debito' ? 'selected' : '' }}>Cartão Débito</option>
                    <option value="pix" {{ $request->forma_pagamento == 'pix' ? 'selected' : '' }}>PIX</option>
                    <option value="transferencia" {{ $request->forma_pagamento == 'transferencia' ? 'selected' : '' }}>Transferência</option>
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
                <a href="{{ route('relatorios.pagamentos') }}" class="btn btn-secondary">
                    <i class="bi bi-eraser"></i> Limpar Filtros
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Cards de Resumo -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6 class="card-title">Total Recebido</h6>
                <h3 class="card-text">R$ {{ number_format($totais['valor_total'], 2, ',', '.') }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h6 class="card-title">Quantidade de Pagamentos</h6>
                <h3 class="card-text">{{ $totais['quantidade'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="card-title">Ticket Médio</h6>
                <h3 class="card-text">
                    R$ {{ number_format($totais['quantidade'] > 0 ? $totais['valor_total'] / $totais['quantidade'] : 0, 2, ',', '.') }}
                </h3>
            </div>
        </div>
    </div>
</div>

<!-- Totais por Forma de Pagamento -->
@if($totais['por_forma']->count() > 0)
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 fw-bold text-primary">Totais por Forma de Pagamento</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Forma de Pagamento</th>
                        <th>Quantidade</th>
                        <th>Total</th>
                        <th>%</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($totais['por_forma'] as $forma)
                    <tr>
                        <td>
                            @if($forma->forma_pagamento == 'dinheiro') 💵 Dinheiro
                            @elseif($forma->forma_pagamento == 'cartao_credito') 💳 Cartão Crédito
                            @elseif($forma->forma_pagamento == 'cartao_debito') 💳 Cartão Débito
                            @elseif($forma->forma_pagamento == 'pix') 📱 PIX
                            @else 🔄 Transferência
                            @endif
                        </td>
                        <td>{{ $forma->quantidade }}</td>
                        <td>R$ {{ number_format($forma->total, 2, ',', '.') }}</td>
                        <td>{{ number_format(($forma->total / $totais['valor_total']) * 100, 1) }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<!-- Lista de Pagamentos -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 fw-bold text-primary">Lista de Pagamentos</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="pagamentosTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Data</th>
                        <th>Cliente</th>
                        <th>Venda</th>
                        <th>Valor</th>
                        <th>Forma</th>
                        <th>Responsável</th>
                        <th>Observação</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pagamentos as $pagamento)
                    <tr>
                        <td>{{ $pagamento->id }}</td>
                        <td>{{ $pagamento->data_pagamento->format('d/m/Y') }}</td>
                        <td>{{ $pagamento->venda->cliente->nome ?? 'N/A' }}</td>
                        <td>#{{ $pagamento->venda_id }}</td>
                        <td class="text-success fw-bold">R$ {{ number_format($pagamento->valor, 2, ',', '.') }}</td>
                        <td>
                            @if($pagamento->forma_pagamento == 'dinheiro') 💵 Dinheiro
                            @elseif($pagamento->forma_pagamento == 'cartao_credito') 💳 Cartão Crédito
                            @elseif($pagamento->forma_pagamento == 'cartao_debito') 💳 Cartão Débito
                            @elseif($pagamento->forma_pagamento == 'pix') 📱 PIX
                            @else 🔄 Transferência
                            @endif
                        </td>
                        <td>{{ $pagamento->user->name ?? 'Sistema' }}</td>
                        <td>{{ $pagamento->observacao ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">Nenhum pagamento encontrado</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            {{ $pagamentos->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#pagamentosTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json'
            },
            order: [[1, 'desc']],
            pageLength: 25,
            responsive: true
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
        .card {
            break-inside: avoid;
        }
    }
</style>
@endpush