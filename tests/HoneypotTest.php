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
    function (array $config
    ) {
        withGraphQLRequest();
        $response = Honeypot::formTokenErrorResponse();

        expect($response->getStatusCode())
            ->toBe(200)
            ->and($response->getContent())
            ->toEqual(json_encode(['errors' => ['message' => 'Internal Server Error']]))
            ->and($response->headers->contains($config['header'], Str::uuid()->toString()))
            ->toBeTrue();
    })
->with('config');
