<?php

namespace Hettiger\Honeypot\Http\Middleware;

use Closure;
use GraphQL\Error\Error as GraphQLError;
use Hettiger\Honeypot\FormToken;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleFormTokenRequests
{
    use RecognizesFormTokenRequests;

    public function __construct(
        protected array $config,
    ) {
    }

    public function handle(Request $request, Closure $next)
    {
        if (! $this->isFormTokenRequest()) {
            return $next($request);
        }

        if ($this->isGraphQLRequest()) {
            throw_unless(
                $this->token()->isValid(),
                new GraphQLError(Response::$statusTexts[Response::HTTP_INTERNAL_SERVER_ERROR])
            );
        } else {
            abort_unless(
                $this->token()->isValid(),
                Response::HTTP_INTERNAL_SERVER_ERROR,
                headers: $this->newTokenHeader()
            );
        }

        return $this->responseWithNewTokenHeader($next($request));
    }

    protected function isGraphQLRequest(): bool
    {
        return request()->routeIs('graphql');
    }

    protected function token(): FormToken
    {
        return FormToken::fromId($this->tokenId());
    }

    protected function tokenId(): ?string
    {
        return request()->headers->get($this->tokenHeaderName());
    }

    protected function newTokenHeader(): array
    {
        return [$this->tokenHeaderName() => FormToken::make()->persisted()->id];
    }

    protected function responseWithNewTokenHeader(mixed $response): Response
    {
        if (! ($response instanceof Response)) {
            $response = response($response);
        }

        $response->headers->add($this->newTokenHeader());

        return $response;
    }
}
