<?php

namespace Hettiger\Honeypot\Http\Middleware;

use Closure;
use function Hettiger\Honeypot\config;
use Hettiger\Honeypot\Facades\Honeypot;
use Illuminate\Http\Request;

class AbortWhenHoneypotIsFilled
{
    public function handle(Request $request, Closure $next)
    {
        abort_if(
            $request->filled(config('field')),
            Honeypot::honeypotErrorResponse(),
        );

        return $next($request);
    }
}
