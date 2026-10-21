<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EhAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check() || ! auth()->user()->ehAdmin()) {
            abort(403, 'Apenas o administrador pode acessar esta página.');
        }

        return $next($request);
    }
}
