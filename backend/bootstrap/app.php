<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// PASTIKAN ADA KATA 'return' DI BAWAH INI
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        
        // 1. Alias Middleware Anda
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);

        // 2. Kecualikan CSRF untuk Callback Midtrans
        $middleware->validateCsrfTokens(except: [
            'api/midtrans/callback',
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();