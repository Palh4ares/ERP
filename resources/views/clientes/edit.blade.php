@extends('layouts.app')
@section('content')
<div class="card">
    <div class="card-header"><h4>Editar Cliente</h4></div>
    <div class="card-body">
        <form action="{{ route('clientes.update', $cliente) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label for="nome" class="form-label">Nome *</label>
                <input type="text" class="form-control" id="nome" name="nome" value="{{ $cliente->nome }}" required>
            </div>
            <div class="mb-3">
                <label for="telefone" class="form-label">Telefone *</label>
                <input type="text" class="form-control" id="telefone" name="telefone" value="{{ $cliente->telefone }}" required>
            </div>
            <button type="submit" class="btn btn-primary">Atualizar</button>
            <a href="{{ route('clientes.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
@endsection