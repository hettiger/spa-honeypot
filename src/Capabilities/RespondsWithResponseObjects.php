<?php

namespace Hettiger\Honeypot\Capabilities;

use Illuminate\Contracts\Support\Responsable;
use Symfony\Component\HttpFoundation\Response;

trait RespondsWithResponseObjects
{
    protected function response(mixed $response): Response
    {
        if ($response instanceof Responsable) {
            $response = $response->toResponse(request());
        }

        if (! ($response instanceof Response)) {
            $response = response($response);
        }

        return $response;
    }
}
