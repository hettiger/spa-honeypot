<?php

namespace Hettiger\Honeypot\Capabilities;

use Hettiger\Honeypot\FormToken;
use Illuminate\Contracts\Support\Responsable;
use Symfony\Component\HttpFoundation\Response;

trait RespondsWithNewFormToken
{
    use InteractsWithFormTokens;

    protected function response(mixed $response, $withNewToken = true): Response
    {
        if ($response instanceof Responsable) {
            $response = $response->toResponse(request());
        }

        if (! ($response instanceof Response)) {
            $response = response($response);
        }

        if ($withNewToken) {
            $response->headers->add($this->newTokenHeader());
        }

        return $response;
    }

    protected function newTokenHeader(): array
    {
        return [$this->tokenHeaderName() => FormToken::make()->persisted()->id];
    }
}
