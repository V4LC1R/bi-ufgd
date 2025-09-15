<?php

use App\Modules\Auth\Http\Middlewares\JWTMiddleware;
use App\Shared\Http\Middleware\ForceJsonResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting()
    ->withProviders(
       [
            //App\Providers\RouteServiceProvider::class
       ]
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->group('api', [
            ForceJsonResponse::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
    })->create();
