<?php

namespace Hettiger\Honeypot;

use Hettiger\Honeypot\Capabilities\RecognizesGraphQLRequests;
use Hettiger\Honeypot\Capabilities\RespondsWithNewFormToken;
use Symfony\Component\HttpFoundation\Response;

class Honeypot
{
    use RecognizesGraphQLRequests;
    use RespondsWithNewFormToken;

    public function __construct(
        protected array $config,
    ) {
    }

    public function formTokenErrorResponse($withNewToken = true)
    {
        if ($this->isGraphQLRequest()) {
            return $this->response([
                'errors' => [
                    'message' => Response::$statusTexts[Response::HTTP_INTERNAL_SERVER_ERROR],
                ],
            ], $withNewToken);
        }

        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }
}
