<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin.active' => \App\Http\Middleware\EnsureAdminIsActive::class,
        ]);

        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('user') || $request->is('user/*')) {
                return route('user.login');
            }

            return route('admin.login');
        });

        $middleware->redirectUsersTo(function (Request $request) {
            if (\Illuminate\Support\Facades\Auth::guard('customer')->check() || $request->is('user') || $request->is('user/*')) {
                return route('user.dashboard');
            }

            return route('admin.dashboard');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
