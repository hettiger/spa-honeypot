<?php

namespace Hettiger\Honeypot\GraphQL\Exceptions;

use Closure;
use GraphQL\Error\Error;

class ErrorHandler implements \Nuwave\Lighthouse\Execution\ErrorHandler
{
    /**
     * {@inheritDoc}
     *
     * @throws ClientSafeHttpResponseException
     */
    public function __invoke(?Error $error, Closure $next): ?array
    {
        $originalException = $error->getPrevious();

        if ($originalException instanceof ClientSafeHttpResponseException) {
            throw $originalException;
        }

        return $next($error);
    }
}
