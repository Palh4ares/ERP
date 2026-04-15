@extends('layouts.app')

@section('title', 'Clientes')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h2">Clientes</h1>
        <a href="{{ route('clientes.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Novo Cliente
        </a>
    </div>
@endsection

@section('content')
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
                        <th>Total Compras</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($clientes as $cliente)
                    <tr>
                        <td>{{ $cliente->id }}</td>
                        <td>{{ $cliente->nome }}</td>
                        <td>{{ $cliente->telefone }}</td>
                        <td>R$ {{ number_format($cliente->total_compras ?? 0, 2, ',', '.') }}</td>
                        <td>
                            <span class="badge bg-{{ $cliente->status == 'ativo' ? 'success' : 'danger' }}">
                                {{ ucfirst($cliente->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('clientes.show', $cliente) }}" class="btn btn-sm btn-info" title="Ver">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-sm btn-warning" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('clientes.destroy', $cliente) }}" method="POST" id="delete-form-{{ $cliente->id }}" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-danger" title="Excluir" onclick="confirmDelete('delete-form-{{ $cliente->id }}')">
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
        $('#clientesTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json'
            },
            order: [[0, 'desc']]
        });
    });
    
    function confirmDelete(formId) {
        Swal.fire({
            title: 'Confirmar exclusão',
            text: 'Tem certeza que deseja excluir este cliente?',
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