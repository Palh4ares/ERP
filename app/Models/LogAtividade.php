<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogAtividade extends Model
{
    protected $fillable = [
        'user_id', 'acao', 'tabela', 'registro_id', 
        'dados_antigos', 'dados_novos', 'ip_address', 'user_agent'
    ];

    protected $casts = [
        'dados_antigos' => 'array',
        'dados_novos' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}