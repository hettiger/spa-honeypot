<?php

namespace Hettiger\Honeypot\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireFormToken
{
    use RecognizesFormTokenRequests;

    public function __construct(
        protected array $config,
    ) {
    }

    public function handle(Request $request, Closure $next)
    {
        abort_unless(
            $this->isFormTokenRequest(),
            Response::HTTP_INTERNAL_SERVER_ERROR,
        );

        return $next($request);
    }
}
