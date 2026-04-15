@extends('layouts.app')

@section('title', 'Detalhes do Cliente')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h2">Detalhes do Cliente</h1>
        <div>
            <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Editar
            </a>
            <a href="{{ route('clientes.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <!-- Informações Pessoais -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 fw-bold text-primary">Informações Pessoais</h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px; font-size: 40px;">
                        <i class="bi bi-person"></i>
                    </div>
                </div>
                
                <table class="table table-borderless">
                    <tr>
                        <th><i class="bi bi-person-badge"></i> Nome:</th>
                        <td>{{ $cliente->nome }}</td>
                    </tr>
                    <tr>
                        <th><i class="bi bi-telephone"></i> Telefone:</th>
                        <td>{{ $cliente->telefone }}</td>
                    </tr>
                    @if($cliente->email)
                    <tr>
                        <th><i class="bi bi-envelope"></i> Email:</th>
                        <td>{{ $cliente->email }}</td>
                    </tr>
                    @endif
                    @if($cliente->cpf_cnpj)
                    <tr>
                        <th><i class="bi bi-credit-card"></i> CPF/CNPJ:</th>
                        <td>{{ $cliente->cpf_cnpj }}</td>
                    </tr>
                    @endif
                    @if($cliente->data_nascimento)
                    <tr>
                        <th><i class="bi bi-calendar"></i> Nascimento:</th>
                        <td>{{ $cliente->data_nascimento->format('d/m/Y') }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th><i class="bi bi-tag"></i> Status:</th>
                        <td>
                            <span class="badge bg-{{ $cliente->status == 'ativo' ? 'success' : 'danger' }}">
                                {{ ucfirst($cliente->status) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th><i class="bi bi-calendar-plus"></i> Cadastro:</th>
                        <td>{{ $cliente->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Endereço -->
        @if($cliente->endereco || $cliente->cidade)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 fw-bold text-primary">Endereço</h6>
            </div>
            <div class="card-body">
                <p><strong>Endereço:</strong> {{ $cliente->endereco ?? 'Não informado' }}</p>
                <p><strong>Cidade/UF:</strong> {{ $cliente->cidade ?? '' }} {{ $cliente->estado ? '/ ' . $cliente->estado : '' }}</p>
                <p><strong>CEP:</strong> {{ $cliente->cep ?? 'Não informado' }}</p>
            </div>
        </div>
        @endif
        
        <!-- Resumo Financeiro -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 fw-bold text-primary">Resumo Financeiro</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label>Total em Compras</label>
                    <h4 class="text-primary">R$ {{ number_format($cliente->total_compras, 2, ',', '.') }}</h4>
                </div>
                <div class="mb-3">
                    <label>Total Pago</label>
                    <h4 class="text-success">R$ {{ number_format($cliente->total_pago, 2, ',', '.') }}</h4>
                </div>
                <div class="mb-3">
                    <label>Saldo Devedor</label>
                    <h4 class="text-danger">R$ {{ number_format($cliente->total_devido, 2, ',', '.') }}</h4>
                </div>
                @if($cliente->limite_credito > 0)
                <div class="mb-3">
                    <label>Limite de Crédito</label>
                    <h4>R$ {{ number_format($cliente->limite_credito, 2, ',', '.') }}</h4>
                    @if($cliente->total_devido > $cliente->limite_credito)
                        <div class="alert alert-danger mt-2">
                            <i class="bi bi-exclamation-triangle"></i> Cliente estourou o limite de crédito!
                        </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <!-- Últimas Vendas -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold text-primary">Histórico de Vendas</h6>
                <a href="{{ route('vendas.create', ['cliente_id' => $cliente->id]) }}" class="btn btn-sm btn-success">
                    <i class="bi bi-cart-plus"></i> Nova Venda
                </a>
            </div>
            <div class="card-body">
                @if($cliente->vendas->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nº Venda</th>
                                    <th>Data</th>
                                    <th>Valor</th>
                                    <th>Pago</th>
                                    <th>Saldo</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cliente->vendas as $venda)
                                <tr>
                                    <td>#{{ $venda->id }}</td>
                                    <td>{{ $venda->data_venda->format('d/m/Y') }}</td>
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
                                    <td>
                                        <a href="{{ route('vendas.show', $venda) }}" class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if($venda->saldo > 0)
                                            <a href="{{ route('pagamentos.create', $venda) }}" class="btn btn-sm btn-success">
                                                <i class="bi bi-cash"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-primary">
                                    <th colspan="2">Totais:</th>
                                    <th>R$ {{ number_format($cliente->vendas->sum('valor_final'), 2, ',', '.') }}</th>
                                    <th>R$ {{ number_format($cliente->vendas->sum('total_pago'), 2, ',', '.') }}</th>
                                    <th>R$ {{ number_format($cliente->vendas->sum('saldo'), 2, ',', '.') }}</th>
                                    <th colspan="2"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-cart-x" style="font-size: 48px; color: #ccc;"></i>
                        <p class="mt-2">Nenhuma venda encontrada para este cliente.</p>
                        <a href="{{ route('vendas.create', ['cliente_id' => $cliente->id]) }}" class="btn btn-primary">
                            <i class="bi bi-cart-plus"></i> Criar primeira venda
                        </a>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Histórico de Pagamentos -->
        @php
            $pagamentos = \App\Models\Pagamento::whereHas('venda', function($q) use ($cliente) {
                $q->where('cliente_id', $cliente->id);
            })->orderBy('created_at', 'desc')->limit(10)->get();
        @endphp
        
        @if($pagamentos->count() > 0)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 fw-bold text-primary">Últimos Pagamentos</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Venda</th>
                                <th>Valor</th>
                                <th>Forma</th>
                                <th>Observação</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pagamentos as $pagamento)
                            <tr>
                                <td>{{ $pagamento->data_pagamento->format('d/m/Y') }}</td>
                                <td><a href="{{ route('vendas.show', $pagamento->venda_id) }}">#{{ $pagamento->venda_id }}</a></td>
                                <td class="text-success">R$ {{ number_format($pagamento->valor, 2, ',', '.') }}</td>
                                <td>
                                    @if($pagamento->forma_pagamento == 'dinheiro') 💵 Dinheiro
                                    @elseif($pagamento->forma_pagamento == 'cartao_credito') 💳 Cartão Crédito
                                    @elseif($pagamento->forma_pagamento == 'cartao_debito') 💳 Cartão Débito
                                    @elseif($pagamento->forma_pagamento == 'pix') 📱 PIX
                                    @else 🔄 Transferência
                                    @endif
                                </td>
                                <td>{{ $pagamento->observacao ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
</script>
@endpush