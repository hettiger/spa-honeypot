<?php

namespace Hettiger\Honeypot\Http\Middleware;

use Closure;
use function Hettiger\Honeypot\config;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AbortWhenHoneypotIsFilled
{
    public function handle(Request $request, Closure $next)
    {
        abort_if(
            $request->filled(config('field')),
            Response::HTTP_INTERNAL_SERVER_ERROR,
        );

        return $next($request);
    }
}
