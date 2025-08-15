<?php

use Illuminate\Foundation\Application;
use App\Http\Middleware\LanguageSwitcher;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: [
            __DIR__ . '/../routes/web/web.php',
            __DIR__ . '/../routes/web/auth.php',
            __DIR__ . '/../routes/web/upload.php',
        ],
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'lang' => LanguageSwitcher::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
