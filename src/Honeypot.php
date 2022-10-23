<?php

namespace Hettiger\Honeypot;

use Closure;
use Hettiger\Honeypot\Capabilities\RecognizesGraphQLRequests;
use Hettiger\Honeypot\Capabilities\RespondsWithNewFormToken;
use Symfony\Component\HttpFoundation\Response;

class Honeypot
{
    use RecognizesGraphQLRequests;
    use RespondsWithNewFormToken;

    protected ?Closure $makeFormTokenErrorResponse = null;

    public function __construct(
        protected array $config,
    ) {
    }

    public function respondToFormTokenErrorsUsing(Closure $makeResponse) {
        $this->makeFormTokenErrorResponse = $makeResponse;
    }

    public function formTokenErrorResponse($withNewToken = true)
    {
        if ($this->makeFormTokenErrorResponse) {
            return $this->response(
                ($this->makeFormTokenErrorResponse)($this->isGraphQLRequest()),
                $withNewToken
            );
        }

        if ($this->isGraphQLRequest()) {
            return $this->response(
                [
                    'errors' => [
                        'message' => Response::$statusTexts[Response::HTTP_INTERNAL_SERVER_ERROR],
                    ],
                ],
                $withNewToken
            );
        }

        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }
}
