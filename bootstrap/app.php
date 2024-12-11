<?php

use App\Http\Middleware\AdminCheck;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',  // Ensure API routes are included
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->appendToGroup('admin.check', [
            AdminCheck::class,
          ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Add custom exception handling if needed
    })
    ->create();
