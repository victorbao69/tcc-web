<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EhEmpresa
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check() || ! auth()->user()->ehEmpresa()) {
            abort(403, 'Apenas empresas podem acessar esta página.');
        }

        return $next($request);
    }
}
