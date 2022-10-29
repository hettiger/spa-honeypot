<?php

namespace Hettiger\Honeypot\Capabilities;

use Hettiger\Honeypot\FormToken;
use Symfony\Component\HttpFoundation\Response;

trait RespondsWithNewFormTokens
{
    use InteractsWithFormTokens;
    use RespondsWithResponseObjects;

    protected function responseWithNewFormToken(mixed $response): Response
    {
        $response = $this->response($response);

        $response->headers->add($this->newTokenHeader());

        return $response;
    }

    protected function newTokenHeader(): array
    {
        return [$this->tokenHeaderName() => FormToken::make()->persisted()->id];
    }
}
