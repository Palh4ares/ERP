<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pagamento extends Model
{
    protected $fillable = [
        'venda_id', 'user_id', 'valor', 'forma_pagamento', 
        'numero_parcela', 'data_pagamento', 'observacao'
    ];

    protected $casts = [
        'data_pagamento' => 'date',
        'valor' => 'decimal:2'
    ];

    public function venda()
    {
        return $this->belongsTo(Venda::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
        static::created(function ($pagamento) {
            $pagamento->venda->atualizarStatus();
        });

        static::deleted(function ($pagamento) {
            $pagamento->venda->atualizarStatus();
        });
    }
}