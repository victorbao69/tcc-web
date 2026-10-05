<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'empresa' => \App\Http\Middleware\EhEmpresa::class,
        ]);

        $middleware->redirectGuestsTo(fn () => route('welcome'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Qualquer erro em /api/* (401, 404, 422...) sempre responde em JSON,
        // mesmo que o cliente esqueça o cabeçalho "Accept: application/json"
        $exceptions->shouldRenderJsonWhen(function (Request $request) {
            return $request->is('api/*') || $request->expectsJson();
        });
    })->create();
