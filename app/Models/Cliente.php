<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = [
        'nome', 'email', 'telefone', 'celular', 'cpf_cnpj', 'data_nascimento',
        'endereco', 'cidade', 'estado', 'cep', 'observacoes', 'limite_credito', 'status'
    ];

    protected $casts = [
        'data_nascimento' => 'date',
        'limite_credito' => 'decimal:2'
    ];

    public function vendas()
    {
        return $this->hasMany(Venda::class);
    }

    public function getTotalDevidoAttribute()
    {
        return $this->vendas()->where('status', '!=', 'pago')->sum('valor_final') - 
               $this->vendas()->where('status', '!=', 'pago')->with('pagamentos')->get()->sum(function($venda) {
                   return $venda->pagamentos->sum('valor');
               });
    }

    public function getTotalComprasAttribute()
    {
        return $this->vendas()->sum('valor_final');
    }
}