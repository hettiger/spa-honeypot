<?php

use function Hettiger\Honeypot\config;
use Hettiger\Honeypot\Facades\Honeypot;
use Illuminate\Support\Str;

beforeEach(function () {
    Str::freezeUuids();
});

test('honeypotErrorResponse returns HTTP status code 500', function () {
    expect(Honeypot::honeypotErrorResponse())
        ->toEqual(500);
});

test('formTokenErrorResponse returns HTTP status code 500', function () {
    expect(Honeypot::formTokenErrorResponse())
        ->toEqual(500);
});

test(
    'honeypotErrorResponse returns GraphQL error response on GraphQL requests',
    function () {
        withGraphQLRequest();
        $response = Honeypot::honeypotErrorResponse();

        expect($response->getStatusCode())
            ->toBe(200)
            ->and($response->getContent())
            ->toEqual(json_encode(['errors' => [['message' => 'Internal Server Error']]]));
    }
);

test(
    'formTokenErrorResponse returns GraphQL error response on GraphQL requests',
    function () {
        withGraphQLRequest();
        $response = Honeypot::formTokenErrorResponse();

        expect($response->getStatusCode())
            ->toBe(200)
            ->and($response->getContent())
            ->toEqual(json_encode(['errors' => [['message' => 'Internal Server Error']]]))
            ->and($response->headers->contains(config('header'), Str::uuid()->toString()))
            ->toBeTrue();
    }
);

test(
    'honeypotErrorResponse returns custom response when available',
    function (bool $isGraphQLRequest) {
        $isGraphQLRequest ? withGraphQLRequest() : withRequest();
        $expectedResponse = response('response fake');
        $expectedGraphQLResponse = response('GraphQL response fake');

        Honeypot::respondToHoneypotErrorsUsing(fn (bool $isGraphQLRequest) => $isGraphQLRequest
            ? $expectedGraphQLResponse
            : $expectedResponse
        );

        $actualResponse = Honeypot::honeypotErrorResponse();

        expect($actualResponse)->toBe($isGraphQLRequest ? $expectedGraphQLResponse : $expectedResponse);
    }
)
->with([
    'normal request' => false,
    'GraphQL request' => true,
]);

test(
    'formTokenErrorResponse returns custom response when available',
    function (bool $withNewToken, bool $isGraphQLRequest) {
        $isGraphQLRequest ? withGraphQLRequest() : withRequest();
        $expectedResponse = response('response fake');
        $expectedGraphQLResponse = response('GraphQL response fake');

        Honeypot::respondToFormTokenErrorsUsing(fn (bool $isGraphQLRequest) => $isGraphQLRequest
            ? $expectedGraphQLResponse
            : $expectedResponse
        );

        $actualResponse = Honeypot::formTokenErrorResponse($withNewToken);

        expect($actualResponse)
            ->toBe($isGraphQLRequest ? $expectedGraphQLResponse : $expectedResponse)
            ->and($actualResponse->headers->contains(config('header'), Str::uuid()->toString()))
            ->toBe($withNewToken);
    }
)
->with([
    'new token' => [true, false],
    'without token' => [false, false],
    'new token, GraphQL' => [true, true],
    'without token, GraphQL' => [false, true],
]);
