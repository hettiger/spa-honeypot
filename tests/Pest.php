<?php

use function Hettiger\Honeypot\config;
use Hettiger\Honeypot\Tests\Features\FeatureTestHelpers;
use Hettiger\Honeypot\Tests\TestCase;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use function Pest\Laravel\swap;
use function Pest\Laravel\travel;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

uses(TestCase::class)->in(__DIR__);
uses(FeatureTestHelpers::class)->in('Features');

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

function simulateSlowHuman()
{
    travel(config('min_age')->totalSeconds)->seconds();
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

expect()->extend('toAbortWithResponse', function (Response $response, ?Closure $headers = null) {
    $headers = $headers ?? fn () => true;
    $this->toThrow(
        fn (HttpResponseException $exception) => expect($exception->getResponse())
            ->toBe($response)
            ->and($headers($exception->getResponse()->headers))
            ->toBeTruthy()
    );
});
