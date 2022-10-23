<?php

namespace Hettiger\Honeypot;

use Symfony\Component\HttpFoundation\Response;

class FormTokenErrorResponseFactory
{
    public function __invoke(bool $isGraphQLRequest): mixed
    {
        $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;

        return $isGraphQLRequest
            ? [
                'errors' => [
                    ['message' => Response::$statusTexts[$statusCode]],
                ],
            ]
            : $statusCode;
    }
}
