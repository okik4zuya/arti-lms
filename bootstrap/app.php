<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'course.access' => \App\Http\Middleware\EnsureCourseAccess::class,
        ]);

        // One-click unsubscribe (RFC 8058): the mail provider POSTs here with no session, so no CSRF token.
        // The signed URL is what authorises it.
        $middleware->validateCsrfTokens(except: ['berhenti/*']);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
