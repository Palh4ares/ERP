@extends('layouts.app')
@section('content')
<div class="card">
    <div class="card-header"><h4>Editar Venda</h4></div>
    <div class="card-body">
        <form action="{{ route('vendas.update', $venda) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">Cliente *</label>
                <select class="form-control" name="cliente_id" required>
                    @foreach($clientes as $cliente)
                    <option value="{{ $cliente->id }}" {{ $venda->cliente_id == $cliente->id ? 'selected' : '' }}>{{ $cliente->nome }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Valor Total *</label>
                <input type="number" step="0.01" class="form-control" name="valor_total" value="{{ $venda->valor_total }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Data *</label>
                <input type="date" class="form-control" name="data_venda" value="{{ $venda->data_venda->format('Y-m-d') }}" required>
            </div>
            <button type="submit" class="btn btn-primary">Atualizar</button>
            <a href="{{ route('vendas.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
@endsection