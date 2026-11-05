<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Sentry\Laravel\Integration;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withExceptions(function (Exceptions $exceptions) {
        Integration::handles($exceptions);
    })
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [\App\Http\Middleware\AdminSitePolicy::class]);
        $middleware->validateCsrfTokens([
            '/logout',
            '/marketplace/productinfo',
            '/asset/GetScriptState.ashx',
            '/Game/PlaceLauncher.ashx',
            '/api/gameserver/update',
            '/Game/Badge/AwardBadge.ashx'
        ]);
    })
    ->create();
