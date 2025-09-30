<?php

namespace Hettiger\Honeypot\Http\Middleware;

use Closure;
use Hettiger\Honeypot\Capabilities\RecognizesFormTokenRequests;
use Hettiger\Honeypot\Capabilities\RespondsWithNewFormTokens;
use Hettiger\Honeypot\Facades\Honeypot;
use Illuminate\Http\Request;

use function Hettiger\Honeypot\config;

class HandleFormTokenRequests
{
    use RecognizesFormTokenRequests;
    use RespondsWithNewFormTokens;

    public function handle(Request $request, Closure $next)
    {
        if (! config('enabled') || ! $this->isFormTokenRequest()) {
            return $next($request);
        }

        abort_unless(
            $this->token()->isValid(),
            Honeypot::formTokenErrorResponse(),
            headers: $this->newTokenHeader()
        );

        return $this->responseWithNewFormToken($next($request));
    }
}
