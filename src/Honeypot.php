<?php

namespace Hettiger\Honeypot;

use Closure;
use Hettiger\Honeypot\Capabilities\RecognizesGraphQLRequests;
use Hettiger\Honeypot\Capabilities\RespondsWithNewFormTokens;
use Illuminate\Contracts\Support\Responsable;
use Symfony\Component\HttpFoundation\Response;

class Honeypot
{
    use RecognizesGraphQLRequests;
    use RespondsWithNewFormTokens;

    protected Closure $makeHoneypotErrorResponse;

    protected Closure $makeFormTokenErrorResponse;

    public function __construct()
    {
        $this->makeHoneypotErrorResponse = resolve(config('honeypot_error_response_factory'))(...);
        $this->makeFormTokenErrorResponse = resolve(config('form_token_error_response_factory'))(...);
    }

    public function respondToHoneypotErrorsUsing(Closure $makeResponse): void
    {
        $this->makeHoneypotErrorResponse = $makeResponse;
    }

    public function respondToFormTokenErrorsUsing(Closure $makeResponse): void
    {
        $this->makeFormTokenErrorResponse = $makeResponse;
    }

    public function honeypotErrorResponse(): Response|Responsable|int
    {
        $response = ($this->makeHoneypotErrorResponse)($this->isGraphQLRequest());

        return is_int($response) ? $response : $this->response($response);
    }

    public function formTokenErrorResponse($withNewToken = true): Response|Responsable|int
    {
        $response = ($this->makeFormTokenErrorResponse)($this->isGraphQLRequest());
        $transform = $withNewToken ? [$this, 'responseWithNewFormToken'] : [$this, 'response'];

        return is_int($response) ? $response : $transform($response);
    }
}
