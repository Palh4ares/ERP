<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\VendaController;
use App\Http\Controllers\PagamentoController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Rota principal - redireciona para login
Route::get('/', function () {
    return redirect()->route('login');
});

// ==================== ROTAS DE AUTENTICAÇÃO ====================
Route::middleware('guest')->group(function () {
    // Login
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    
    // Registro
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);
    
    // Recuperar senha
    Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// ==================== ROTAS PROTEGIDAS (REQUEREM LOGIN) ====================
Route::middleware(['auth'])->group(function () {
    
    // Dashboard (rota principal após login)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Redirecionar /home para /dashboard
    Route::get('/home', function () {
        return redirect()->route('dashboard');
    });
    
    // ==================== CLIENTES ====================
    Route::resource('clientes', ClienteController::class);
    
    // ==================== VENDAS ====================
    Route::resource('vendas', VendaController::class);
    
    // ==================== PAGAMENTOS ====================
    Route::get('pagamentos/create/{venda}', [PagamentoController::class, 'create'])->name('pagamentos.create');
    Route::post('pagamentos/store/{venda}', [PagamentoController::class, 'store'])->name('pagamentos.store');
    Route::delete('pagamentos/{pagamento}', [PagamentoController::class, 'destroy'])->name('pagamentos.destroy');
    
    // ==================== RELATÓRIOS ====================
    Route::prefix('relatorios')->name('relatorios.')->group(function () {
        Route::get('vendas', [RelatorioController::class, 'vendas'])->name('vendas');
        Route::get('clientes', [RelatorioController::class, 'clientes'])->name('clientes');
        Route::get('pagamentos', [RelatorioController::class, 'pagamentos'])->name('pagamentos');
    });
    
    // ==================== PERFIL ====================
    Route::get('perfil', [PerfilController::class, 'edit'])->name('perfil.edit');
    Route::put('perfil', [PerfilController::class, 'update'])->name('perfil.update');

    // RELATÓRIOS
    Route::prefix('relatorios')->name('relatorios.')->group(function () {
    Route::get('vendas', [RelatorioController::class, 'vendas'])->name('vendas');
    Route::get('clientes', [RelatorioController::class, 'clientes'])->name('clientes');
    Route::get('pagamentos', [RelatorioController::class, 'pagamentos'])->name('pagamentos');
});


    });