<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venda extends Model
{
    protected $fillable = [
        'cliente_id', 'user_id', 'numero_venda', 'valor_total', 'desconto',
        'valor_final', 'parcelas', 'data_venda', 'data_vencimento', 'status', 'observacoes'
    ];

    protected $casts = [
        'data_venda' => 'date',
        'data_vencimento' => 'date',
        'valor_total' => 'decimal:2',
        'desconto' => 'decimal:2',
        'valor_final' => 'decimal:2'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pagamentos()
    {
        return $this->hasMany(Pagamento::class);
    }

    public function getTotalPagoAttribute()
    {
        return $this->pagamentos()->sum('valor');
    }

    public function getSaldoAttribute()
    {
        return max(0, $this->valor_final - $this->total_pago);
    }

    public function getPercentualPagoAttribute()
    {
        if ($this->valor_final == 0) return 0;
        return ($this->total_pago / $this->valor_final) * 100;
    }

    public function atualizarStatus()
    {
        if ($this->status === 'cancelado') return;
        
        if ($this->saldo <= 0) {
            $this->status = 'pago';
        } elseif ($this->total_pago > 0 && $this->saldo > 0) {
            $this->status = 'parcial';
        } else {
            $this->status = 'aberto';
        }
        $this->save();
    }
}