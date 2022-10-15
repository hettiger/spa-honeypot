<?php

use Hettiger\Honeypot\Tests\TestCase;
use Symfony\Component\HttpKernel\Exception\HttpException;

uses(TestCase::class)->in(__DIR__);

/**
 * Returns a service of the given `$type` from the container
 *
 * @template T
 *
 * @param  T  $type
 * @param  array  $parameters
 * @return T
 */
function resolveByType(mixed $type, array $parameters = [])
{
    return app($type, $parameters);
}

expect()->extend('toAbortWith', function (int $statusCode, string $message = '', array $headers = []) {
    $this->toThrow(
        fn (HttpException $exception) => expect($exception->getStatusCode())
            ->toEqual($statusCode)
            ->and($exception->getMessage())
            ->toEqual($message)
            ->and($exception->getHeaders())
            ->toEqual($headers)
    );
});
