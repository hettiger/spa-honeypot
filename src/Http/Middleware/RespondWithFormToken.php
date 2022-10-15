<?php

namespace Hettiger\Honeypot\Http\Middleware;

use Closure;
use Hettiger\Honeypot\FormToken;
use Illuminate\Http\Request;

class RespondWithFormToken
{
    public function __construct(
        protected array $config
    ) {
    }

    public function handle(Request $request, Closure $next)
    {
        $header = $this->config['form_token_header'];

        if ($request->headers->has($header) && empty($request->headers->get($header))) {
            return FormToken::make()->persisted()->id;
        }

        return $next($request);
    }
}
