<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Infrastructure\Http\Kernel as HttpKernel;
use App\Infrastructure\Console\Kernel as ConsoleKernel;

return Application::configure(basePath: dirname(__DIR__,3))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Adicione middlewares globais se necessário
    })
    ->withExceptions(function (Exceptions $exceptions) {
        
    })
    ->create();
