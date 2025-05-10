<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ServiceOrderController;

Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/usuarios/cadastrar', [UserController::class, 'create'])->name('users.create');
Route::post('/usuarios', [UserController::class, 'store'])->name('users.store');

Route::middleware('auth')->group(function () {
    Route::get('/ordens', [ServiceOrderController::class, 'index'])->name('service_orders.index');
    Route::get('/ordens/criar', [ServiceOrderController::class, 'create'])->name('service_orders.create');
    Route::post('/ordens', [ServiceOrderController::class, 'store'])->name('service_orders.store');
    Route::get('/ordens/{serviceOrder}/editar', [ServiceOrderController::class, 'edit'])->name('service_orders.edit');
    Route::put('/ordens/{serviceOrder}', [ServiceOrderController::class, 'update'])->name('service_orders.update');
});
