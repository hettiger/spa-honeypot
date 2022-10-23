<?php

use Hettiger\Honeypot\Facades\Honeypot;
use Illuminate\Support\Str;

beforeEach(function () {
    Str::freezeUuids();
});

test('formTokenErrorResponse returns HTTP status code 500', function () {
    expect(Honeypot::formTokenErrorResponse())
        ->toEqual(500);
});

test(
    'formTokenErrorResponse returns GraphQL error response on GraphQL requests',
    function (array $config) {
        withGraphQLRequest();
        $response = Honeypot::formTokenErrorResponse();

        expect($response->getStatusCode())
            ->toBe(200)
            ->and($response->getContent())
            ->toEqual(json_encode(['errors' => [['message' => 'Internal Server Error']]]))
            ->and($response->headers->contains($config['header'], Str::uuid()->toString()))
            ->toBeTrue();
    })
->with('config');

test(
    'formTokenErrorResponse returns custom response when available',
    function (array $config, bool $withNewToken, bool $isGraphQLRequest) {
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
            ->and($actualResponse->headers->contains($config['header'], Str::uuid()->toString()))
            ->toBe($withNewToken);
    }
)
->with('config')
->with([
    'new token' => [true, false],
    'without token' => [false, false],
    'new token, GraphQL' => [true, true],
    'without token, GraphQL' => [false, true],
]);
