<?php

namespace Hettiger\Honeypot\Http\Middleware;

trait RecognizesFormTokenRequests
{
    protected function isFormTokenRequest(): bool
    {
        return request()->headers->has($this->tokenHeaderName());
    }

    protected function tokenHeaderName(): string
    {
        return $this->config['header'];
    }
}
