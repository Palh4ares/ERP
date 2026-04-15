@extends('layouts.app')
@section('content')
<div class="card">
    <div class="card-header"><h4>Novo Cliente</h4></div>
    <div class="card-body">
        <form action="{{ route('clientes.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="nome" class="form-label">Nome *</label>
                <input type="text" class="form-control @error('nome') is-invalid @enderror" id="nome" name="nome" required>
                @error('nome')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="telefone" class="form-label">Telefone *</label>
                <input type="text" class="form-control @error('telefone') is-invalid @enderror" id="telefone" name="telefone" required>
                @error('telefone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="{{ route('clientes.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
@endsection