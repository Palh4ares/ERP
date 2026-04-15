<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Venda;
use App\Models\Pagamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Verificar se usuário está logado
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $totalVendas = Venda::sum('valor_final');
        $totalRecebido = Pagamento::sum('valor');
        $totalAReceber = Venda::where('status', '!=', 'pago')->sum('valor_final') - 
                        Pagamento::whereHas('venda', function($q) {
                            $q->where('status', '!=', 'pago');
                        })->sum('valor');
        
        $totalClientes = Cliente::count();
        
        $vendasRecentes = Venda::with(['cliente', 'pagamentos'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Calcular total pago e saldo para cada venda
        foreach($vendasRecentes as $venda) {
            $venda->total_pago = $venda->pagamentos->sum('valor');
            $venda->saldo = $venda->valor_final - $venda->total_pago;
        }
        
        return view('dashboard.index', compact(
            'totalVendas', 'totalRecebido', 'totalAReceber', 
            'totalClientes', 'vendasRecentes'
        ));
    }
}