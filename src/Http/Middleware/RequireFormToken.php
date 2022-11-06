<?php

namespace Hettiger\Honeypot\Http\Middleware;

use Closure;
use Hettiger\Honeypot\Capabilities\RecognizesFormTokenRequests;
use function Hettiger\Honeypot\config;
use Hettiger\Honeypot\Facades\Honeypot;
use Illuminate\Http\Request;

class RequireFormToken
{
    use RecognizesFormTokenRequests;

    public function handle(Request $request, Closure $next)
    {
        if (! config('enabled')) {
            return $next($request);
        }

        abort_unless(
            $this->isFormTokenRequest(),
            Honeypot::formTokenErrorResponse(false),
        );

        return $next($request);
    }
}
