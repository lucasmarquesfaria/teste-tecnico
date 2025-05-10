<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ServiceOrderController;

// Rotas de autenticação
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest'); // Garante que /login também tem o nome 'login'
Route::post('/login', [AuthController::class, 'login'])->name('login.submit'); // Renomeado para evitar conflitos
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rotas de cadastro
Route::get('/usuarios/cadastrar', [UserController::class, 'create'])->name('users.create');
Route::post('/usuarios', [UserController::class, 'store'])->name('users.store');

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Ordens de serviço
    Route::get('/ordens', [ServiceOrderController::class, 'index'])->name('service_orders.index');
    Route::get('/ordens/criar', [ServiceOrderController::class, 'create'])->name('service_orders.create');
    Route::post('/ordens', [ServiceOrderController::class, 'store'])->name('service_orders.store');
    Route::get('/ordens/{serviceOrder}', [ServiceOrderController::class, 'show'])->name('service_orders.show');
    Route::get('/ordens/{serviceOrder}/editar', [ServiceOrderController::class, 'edit'])->name('service_orders.edit');
    Route::put('/ordens/{serviceOrder}', [ServiceOrderController::class, 'update'])->name('service_orders.update');
    Route::post('/ordens/{serviceOrder}', [ServiceOrderController::class, 'update'])->name('service_orders.update.post'); // Rota alternativa usando POST
    
    // Redirecionar usuários autenticados para o dashboard
    Route::get('/', function() {
        return redirect()->route('dashboard');
    })->middleware('auth');
});
