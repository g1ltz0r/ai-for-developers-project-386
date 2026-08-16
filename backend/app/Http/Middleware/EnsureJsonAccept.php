<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureJsonAccept
{
    /**
     * Ensure the request accepts JSON responses.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->expectsJson()) {
            $request->headers->set('Accept', 'application/json');
        }

        return $next($request);
    }
}
