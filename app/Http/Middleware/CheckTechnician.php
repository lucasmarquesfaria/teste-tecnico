<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckTechnician
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || Auth::user()->role !== 'technician') {
            return redirect()->route('dashboard')
                ->with('error', 'Acesso restrito. Apenas a área técnica pode acessar esta página.');
        }

        return $next($request);
    }
}
