<?php

namespace Hettiger\Honeypot\Http\Middleware;

use Closure;
use Hettiger\Honeypot\Facades\Honeypot;
use Illuminate\Http\Request;

use function Hettiger\Honeypot\config;

class AbortWhenHoneypotIsFilled
{
    public function handle(Request $request, Closure $next)
    {
        if (! config('enabled')) {
            return $next($request);
        }

        abort_if(
            $request->filled(config('field')),
            Honeypot::honeypotErrorResponse(),
        );

        return $next($request);
    }
}
