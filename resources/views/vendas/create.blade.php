@extends('layouts.app')

@section('title', 'Nova Venda')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h2">Nova Venda</h1>
        <a href="{{ route('vendas.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
    </div>
@endsection

@section('content')
<div class="card shadow">
    <div class="card-body">
        <form action="{{ route('vendas.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label for="cliente_id" class="form-label">Cliente *</label>
                <select class="form-control @error('cliente_id') is-invalid @enderror" 
                        id="cliente_id" name="cliente_id" required>
                    <option value="">Selecione um cliente</option>
                    @foreach($clientes as $cliente)
                        <option value="{{ $cliente->id }}" 
                            {{ old('cliente_id', $clienteSelecionado->id ?? '') == $cliente->id ? 'selected' : '' }}>
                            {{ $cliente->nome }} - {{ $cliente->telefone }}
                        </option>
                    @endforeach
                </select>
                @error('cliente_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="valor_total" class="form-label">Valor Total *</label>
                <div class="input-group">
                    <span class="input-group-text">R$</span>
                    <input type="number" step="0.01" class="form-control @error('valor_total') is-invalid @enderror" 
                           id="valor_total" name="valor_total" value="{{ old('valor_total') }}" required>
                </div>
                @error('valor_total')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="desconto" class="form-label">Desconto</label>
                <div class="input-group">
                    <span class="input-group-text">R$</span>
                    <input type="number" step="0.01" class="form-control @error('desconto') is-invalid @enderror" 
                           id="desconto" name="desconto" value="{{ old('desconto', 0) }}">
                </div>
                @error('desconto')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="data_venda" class="form-label">Data da Venda *</label>
                <input type="date" class="form-control @error('data_venda') is-invalid @enderror" 
                       id="data_venda" name="data_venda" value="{{ old('data_venda', date('Y-m-d')) }}" required>
                @error('data_venda')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            
            
            <div class="mb-3">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Salvar Venda
                </button>
                <a href="{{ route('vendas.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

@if($clienteSelecionado)
<div class="alert alert-info mt-3">
    <i class="bi bi-info-circle"></i>
    Criando venda para o cliente: <strong>{{ $clienteSelecionado->nome }}</strong>
</div>
@endif
@endsection

@push('scripts')
<script>
    // Calcular valor final automaticamente
    document.getElementById('valor_total').addEventListener('input', calcularFinal);
    document.getElementById('desconto').addEventListener('input', calcularFinal);
    
    function calcularFinal() {
        const valorTotal = parseFloat(document.getElementById('valor_total').value) || 0;
        const desconto = parseFloat(document.getElementById('desconto').value) || 0;
        const valorFinal = valorTotal - desconto;
        
        // Mostrar valor final se quiser
        console.log('Valor Final: R$ ' + valorFinal.toFixed(2));
    }
</script>
@endpush