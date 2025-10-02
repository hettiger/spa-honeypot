<?php

namespace Hettiger\Honeypot;

use Symfony\Component\HttpFoundation\Response;

class ErrorResponseFactory
{
    public function __invoke(bool $isGraphQLRequest): array|int
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
