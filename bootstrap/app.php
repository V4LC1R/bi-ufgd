<?php

use App\Http\Middleware\ForceJsonResponse;
use App\Http\Middleware\AuthenticateActor;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting()
    ->withProviders(
        [

        ]
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->group('api', [
            ForceJsonResponse::class,
        ]);
        $middleware->alias([
            'auth.actor' => AuthenticateActor::class,
        ]);
        $middleware->redirectGuestsTo(fn() => null);
    })
    ->withExceptions(function (Exceptions $exceptions) {

    })->create();
