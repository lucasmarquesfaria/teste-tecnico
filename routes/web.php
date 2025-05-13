<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ServiceOrderController;
use App\Http\Controllers\AnalyticsController;

Route::get('/', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/usuarios/cadastrar', [UserController::class, 'create'])->name('users.create');
Route::post('/usuarios', [UserController::class, 'store'])->name('users.store');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Rota para analytics usando verificação direta no controller
    Route::get('/analytics', [AnalyticsController::class, 'index'])
        ->name('analytics')
        ->middleware([\App\Http\Middleware\CheckTechnician::class]);
    
    Route::get('/ordens', [ServiceOrderController::class, 'index'])->name('service_orders.index');
    Route::get('/ordens/criar', [ServiceOrderController::class, 'create'])->name('service_orders.create');
    Route::post('/ordens', [ServiceOrderController::class, 'store'])->name('service_orders.store');
    Route::get('/ordens/{serviceOrder}', [ServiceOrderController::class, 'show'])->name('service_orders.show');
    Route::get('/ordens/{serviceOrder}/editar', [ServiceOrderController::class, 'edit'])->name('service_orders.edit');
    Route::put('/ordens/{serviceOrder}', [ServiceOrderController::class, 'update'])->name('service_orders.update');
    
    // Rota para corrigir datas das ordens de serviço (apenas para técnicos)
    Route::get('/corrigir-datas', function() {
        if (auth()->user()->role !== 'technician') {
            abort(403, 'Apenas técnicos podem acessar esta funcionalidade.');
        }
        
        $orders = \App\Models\ServiceOrder::all()->sortByDesc('created_at');
        
        return view('service_orders.fix_dates', ['orders' => $orders]);
    })->name('service_orders.fix_dates');
    
    // Rota para aplicar a correção das datas
    Route::post('/corrigir-datas/aplicar', function() {
        if (auth()->user()->role !== 'technician') {
            abort(403, 'Apenas técnicos podem executar esta ação.');
        }
        
        // Corrigir timestamps duplicados
        $orders = \App\Models\ServiceOrder::all()->sortByDesc('id');
        
        // Certifique-se de que as datas são únicas e ordenadas corretamente
        $count = 0;
        $lastDate = null;
        
        foreach ($orders as $order) {
            if ($lastDate === null) {
                $lastDate = now();
            } else {
                $lastDate = $lastDate->subSeconds(10); // Diferença de 10 segundos entre cada ordem
            }
            
            $order->created_at = $lastDate;
            $order->updated_at = $lastDate;
            $order->save();
            $count++;
        }
        
        return redirect()
            ->route('service_orders.index')
            ->with('success', "Ordenação corrigida com sucesso! Foram atualizadas $count ordens de serviço.");
    })->name('service_orders.apply_fix');
    
    // Relatórios de OS
    Route::get('/relatorios', [App\Http\Controllers\ReportController::class, 'form'])->name('reports.form');
    Route::post('/relatorios/pdf', [App\Http\Controllers\ReportController::class, 'generatePdf'])->name('reports.pdf');
    Route::post('/relatorios/excel', [App\Http\Controllers\ReportController::class, 'generateExcel'])->name('reports.excel');

    Route::get('/', function() {
        return redirect()->route('dashboard');
    })->middleware('auth');
});
