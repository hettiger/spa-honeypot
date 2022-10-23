<?php

namespace Hettiger\Honeypot\Capabilities;

trait RecognizesFormTokenRequests
{
    use InteractsWithFormTokens;

    protected function isFormTokenRequest(): bool
    {
        return request()->headers->has($this->tokenHeaderName());
    }
}
