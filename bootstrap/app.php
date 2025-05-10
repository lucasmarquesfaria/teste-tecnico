<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            // Web middleware group já inclui o VerifyCsrfToken por padrão
            // Não precisamos adicionar explicitamente
        ]);
        
        $middleware->alias([
            'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->reportable(function (\Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $e) {
            // Log específico para o erro que estamos enfrentando
            \Illuminate\Support\Facades\Log::warning('Método HTTP não permitido detectado: ' . $e->getMessage());
        });
    })->create();
