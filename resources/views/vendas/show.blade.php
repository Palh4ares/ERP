@extends('layouts.app')

@section('title', 'Detalhes da Venda')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h2">Venda #{{ $venda->id }}</h1>
        <div>
            <a href="{{ route('vendas.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
            @if($venda->saldo > 0)
                <a href="{{ route('pagamentos.create', $venda) }}" class="btn btn-success">
                    <i class="bi bi-cash"></i> Registrar Pagamento
                </a>
            @endif
        </div>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 fw-bold text-primary">Informações da Venda</h6>
            </div>
            <div class="card-body">
                <p><strong>Cliente:</strong> {{ $venda->cliente->nome }}</p>
                <p><strong>Telefone:</strong> {{ $venda->cliente->telefone }}</p>
                <p><strong>Valor Total:</strong> R$ {{ number_format($venda->valor_total, 2, ',', '.') }}</p>
                @if($venda->desconto > 0)
                <p><strong>Desconto:</strong> R$ {{ number_format($venda->desconto, 2, ',', '.') }}</p>
                <p><strong>Valor Final:</strong> R$ {{ number_format($venda->valor_final, 2, ',', '.') }}</p>
                @endif
                <p><strong>Total Pago:</strong> R$ {{ number_format($venda->total_pago, 2, ',', '.') }}</p>
                <p><strong>Saldo Restante:</strong> 
                    <span class="{{ $venda->saldo > 0 ? 'text-danger' : 'text-success' }} fw-bold">
                        R$ {{ number_format($venda->saldo, 2, ',', '.') }}
                    </span>
                </p>
                <p><strong>Status:</strong> 
                    <span class="badge bg-{{ $venda->status == 'aberto' ? 'warning' : ($venda->status == 'parcial' ? 'info' : 'success') }}">
                        {{ ucfirst($venda->status) }}
                    </span>
                </p>
                <p><strong>Data da Venda:</strong> {{ $venda->data_venda->format('d/m/Y') }}</p>
                <p><strong>Vendedor:</strong> {{ $venda->user->name ?? 'Sistema' }}</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 fw-bold text-primary">Histórico de Pagamentos</h6>
            </div>
            <div class="card-body">
                @if($venda->pagamentos->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Valor</th>
                                    <th>Observação</th>
                                    <th>Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($venda->pagamentos as $pagamento)
                                <tr>
                                    <td>{{ $pagamento->data_pagamento->format('d/m/Y') }}</td>
                                    <td>R$ {{ number_format($pagamento->valor, 2, ',', '.') }}</td>
                                    <td>{{ $pagamento->observacao ?? '-' }}</td>
                                    <td>
                                        <form action="{{ route('pagamentos.destroy', $pagamento) }}" method="POST" id="delete-pagamento-{{ $pagamento->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-danger" onclick="confirmDeletePagamento('delete-pagamento-{{ $pagamento->id }}')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center">Nenhum pagamento registrado.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function confirmDeletePagamento(formId) {
        Swal.fire({
            title: 'Confirmar exclusão',
            text: 'Tem certeza que deseja remover este pagamento?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sim, remover!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
    }
</script>
@endpush