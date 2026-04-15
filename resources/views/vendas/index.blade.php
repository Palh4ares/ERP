@extends('layouts.app')

@section('title', 'Vendas')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h2">Vendas</h1>
        <a href="{{ route('vendas.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nova Venda
        </a>
    </div>
@endsection

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 fw-bold text-primary">Lista de Vendas</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="vendasTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Valor Total</th>
                        <th>Total Pago</th>
                        <th>Saldo</th>
                        <th>Status</th>
                        <th>Data</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vendas as $venda)
                    <tr>
                        <td>{{ $venda->id }}</td>
                        <td>{{ $venda->cliente->nome ?? 'N/A' }}</td>
                        <td>R$ {{ number_format($venda->valor_final, 2, ',', '.') }}</td>
                        <td>R$ {{ number_format($venda->total_pago, 2, ',', '.') }}</td>
                        <td>R$ {{ number_format($venda->saldo, 2, ',', '.') }}</td>
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
                         </td>
                        <td>{{ $venda->data_venda->format('d/m/Y') }}</td>
                        <td>
                            <a href="{{ route('vendas.show', $venda) }}" class="btn btn-sm btn-info" title="Ver">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('vendas.edit', $venda) }}" class="btn btn-sm btn-warning" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @if($venda->saldo > 0)
                                <a href="{{ route('pagamentos.create', $venda) }}" class="btn btn-sm btn-success" title="Registrar Pagamento">
                                    <i class="bi bi-cash"></i>
                                </a>
                            @endif
                            <form action="{{ route('vendas.destroy', $venda) }}" method="POST" id="delete-form-{{ $venda->id }}" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-danger" title="Excluir" onclick="confirmDelete('delete-form-{{ $venda->id }}')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                         </td>
                     </tr>
                    @endforeach
                </tbody>
            </table>
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
            order: [[6, 'desc']]
        });
    });
    
    function confirmDelete(formId) {
        Swal.fire({
            title: 'Confirmar exclusão',
            text: 'Tem certeza que deseja excluir esta venda?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sim, excluir!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
    }
</script>
@endpush