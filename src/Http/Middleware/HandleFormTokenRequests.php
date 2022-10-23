<?php

namespace Hettiger\Honeypot\Http\Middleware;

use Closure;
use Hettiger\Honeypot\Capabilities\RecognizesFormTokenRequests;
use Hettiger\Honeypot\Capabilities\RespondsWithNewFormToken;
use Hettiger\Honeypot\Facades\Honeypot;
use Illuminate\Http\Request;

class HandleFormTokenRequests
{
    use RecognizesFormTokenRequests;
    use RespondsWithNewFormToken;

    public function __construct(
        protected array $config,
    ) {
    }

    public function handle(Request $request, Closure $next)
    {
        if (! $this->isFormTokenRequest()) {
            return $next($request);
        }

        abort_unless(
            $this->token()->isValid(),
            Honeypot::formTokenErrorResponse(),
            headers: $this->newTokenHeader()
        );

        return $this->response($next($request));
    }
}
