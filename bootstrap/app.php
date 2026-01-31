<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function ($middleware) {
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\SetLocaleFromSiteSettings::class,
            \App\Http\Middleware\CookieConsentMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $e, $request) {
            if (
                $e instanceof \Symfony\Component\HttpKernel\Exception\HttpException
                && $e->getStatusCode() === 403
                && $request->is('dashboard*')
            ) {
                return response()->view('dashboard.403', [], 403);
            }

            return null;
        });
    })
    ->create();
