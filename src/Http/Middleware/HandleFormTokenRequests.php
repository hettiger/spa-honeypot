<?php

namespace Hettiger\Honeypot\Http\Middleware;

use Closure;
use Hettiger\Honeypot\FormToken;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleFormTokenRequests
{
    public function __construct(
        protected array $config
    ) {
    }

    public function handle(Request $request, Closure $next)
    {
        if (! $this->isFormTokenRequest($request)) {
            return $next($request);
        }

        abort_if(
            ! $this->token($request)->isValid(),
            500,
            headers: $this->headers()
        );

        $response = $next($request);

        if (! ($response instanceof Response)) {
            $response = response($response);
        }

        $response->headers->add($this->headers());

        return $response;
    }

    protected function isFormTokenRequest(Request $request): bool
    {
        return $request->headers->has($this->config['header']);
    }

    protected function token(Request $request): FormToken
    {
        return FormToken::fromId($this->tokenId($request));
    }

    protected function tokenId(Request $request): ?string
    {
        return $request->headers->get($this->config['header']);
    }

    protected function headers(): array
    {
        return [$this->config['header'] => FormToken::make()->persisted()->id];
    }
}
