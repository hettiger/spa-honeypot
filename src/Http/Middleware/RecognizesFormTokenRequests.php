<?php

namespace Hettiger\Honeypot\Http\Middleware;

trait RecognizesFormTokenRequests
{
    protected function isFormTokenRequest(): bool
    {
        return request()->headers->has($this->config['header']);
    }
}
