<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Garantir que o método PUT seja processado corretamente
        \Illuminate\Support\Facades\Route::resourceVerbs([
            'create' => 'criar',
            'edit' => 'editar',
        ]);
    }
}
