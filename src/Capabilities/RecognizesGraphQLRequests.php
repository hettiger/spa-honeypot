<?php

namespace Hettiger\Honeypot\Capabilities;

trait RecognizesGraphQLRequests
{
    protected function isGraphQLRequest(): bool
    {
        return request()->routeIs('graphql');
    }
}
