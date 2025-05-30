<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Infrastructure\Http\Kernel as HttpKernel;
use App\Infrastructure\Console\Kernel as ConsoleKernel;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Adicione middlewares globais se necessário
    })
    ->withExceptions(function (Exceptions $exceptions) {
        
    })
    ->create();

$app->useEnvironmentPath(dirname(__DIR__,3));

return $app;
