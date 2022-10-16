<?php

namespace Hettiger\Honeypot\Http\Middleware;

use Closure;
use Hettiger\Honeypot\FormToken;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleFormTokenRequests
{
    use RecognizesFormTokenRequests;

    public function __construct(
        protected array $config
    ) {
    }

    public function handle(Request $request, Closure $next)
    {
        if (! $this->isFormTokenRequest($request)) {
            return $next($request);
        }

        abort_unless(
            $this->token($request)->isValid(),
            Response::HTTP_INTERNAL_SERVER_ERROR,
            headers: $this->newTokenHeader()
        );

        $response = $next($request);

        if (! ($response instanceof Response)) {
            $response = response($response);
        }

        $response->headers->add($this->newTokenHeader());

        return $response;
    }

    protected function token(Request $request): FormToken
    {
        return FormToken::fromId($this->tokenId($request));
    }

    protected function tokenId(Request $request): ?string
    {
        return $request->headers->get($this->config['header']);
    }

    protected function newTokenHeader(): array
    {
        return [$this->config['header'] => FormToken::make()->persisted()->id];
    }
}
