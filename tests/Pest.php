<?php

use Hettiger\Honeypot\Tests\TestCase;
use Illuminate\Http\Request;
use function Pest\Laravel\swap;
use Symfony\Component\HttpKernel\Exception\HttpException;

uses(TestCase::class)->in(__DIR__);

function makeRequest(): Request
{
    $request = new Request();
    swap('request', $request);

    return $request;
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
