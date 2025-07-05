<?php

namespace App\Shared\Http\Middleware;

use Closure;

class ForceJsonResponse
{
    public function handle($request, Closure $next)
    {
        $request->headers->set('Accept', 'application/json');
        return $next($request);
    }
}
