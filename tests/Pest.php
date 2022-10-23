<?php

use Hettiger\Honeypot\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use function Pest\Laravel\swap;
use Symfony\Component\HttpKernel\Exception\HttpException;

uses(TestCase::class)->in(__DIR__);

function withGraphQLRequest(?Request $request = null): Request
{
    $request = withRequest($request);
    $request->setRouteResolver(fn () => Route::getRoutes()->getByName('graphql'));

    return $request;
}

function withRequest(?Request $request = null): Request
{
    $request = $request ?? request();
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
