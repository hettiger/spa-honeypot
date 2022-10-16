<?php

namespace Hettiger\Honeypot\Http\Middleware;

use Illuminate\Http\Request;

trait RecognizesFormTokenRequests
{
    protected function isFormTokenRequest(Request $request): bool
    {
        return $request->headers->has($this->config['header']);
    }
}
