<?php

namespace Hettiger\Honeypot;

use Closure;
use Hettiger\Honeypot\Capabilities\RecognizesGraphQLRequests;
use Hettiger\Honeypot\Capabilities\RespondsWithNewFormToken;
use Illuminate\Contracts\Support\Responsable;
use Symfony\Component\HttpFoundation\Response;

class Honeypot
{
    use RecognizesGraphQLRequests;
    use RespondsWithNewFormToken;

    protected Closure $makeFormTokenErrorResponse;

    public function __construct(
        protected array $config,
    ) {
        $this->makeFormTokenErrorResponse = fn (bool $isGraphQLRequest) => $isGraphQLRequest
            ? [
                'errors' => [
                    'message' => Response::$statusTexts[Response::HTTP_INTERNAL_SERVER_ERROR],
                ]
            ]
            : Response::HTTP_INTERNAL_SERVER_ERROR;
    }

    public function respondToFormTokenErrorsUsing(Closure $makeResponse) {
        $this->makeFormTokenErrorResponse = $makeResponse;
    }

    public function formTokenErrorResponse($withNewToken = true): Response|Responsable|int
    {
        $response = ($this->makeFormTokenErrorResponse)($this->isGraphQLRequest());

        return is_int($response) ? $response : $this->response($response, $withNewToken);
    }
}
