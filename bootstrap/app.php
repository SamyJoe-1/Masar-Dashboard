<?php

use Illuminate\Foundation\Application;
use App\Http\Middleware\LanguageSwitcher;
use App\Http\Middleware\IsHR;
use App\Http\Middleware\IsApplicant;
use App\Http\Middleware\Profile;
use App\Providers\ObserverProvider;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: [
            __DIR__ . '/../routes/web/general.php',
            __DIR__ . '/../routes/web/hr.php',
            __DIR__ . '/../routes/web/applicant.php',
            __DIR__ . '/../routes/web/guest.php',
            __DIR__ . '/../routes/web/auth.php',
            __DIR__ . '/../routes/web/upload.php',
        ],
        api: [
            __DIR__ . '/../routes/api.php',
        ],
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withProviders([
        ObserverProvider::class,
    ])
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'lang' => LanguageSwitcher::class,
            'profile' => Profile::class,
            'hr' => IsHR::class,
            'applicant' => IsApplicant::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
