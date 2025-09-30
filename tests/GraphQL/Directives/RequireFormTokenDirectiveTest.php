<?php

use Hettiger\Honeypot\Facades\Honeypot;
use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;
use Nuwave\Lighthouse\Testing\MocksResolvers;
use Nuwave\Lighthouse\Testing\UsesTestSchema;

use function Hettiger\Honeypot\config;

uses(
    MakesGraphQLRequests::class,
    UsesTestSchema::class,
    MocksResolvers::class,
);

beforeEach(function () {
    $this->setUpTestSchema();
});

it('bails out when package is not enabled', function (string $schema, string $query) {
    config()->set('spa-honeypot.enabled', false);
    $this->schema = $schema;
    $this->mockResolver()->willReturn('OK');

    /** @noinspection GraphQLUnresolvedReference */
    $this->graphQL($query)
        ->assertExactJson([
            'data' => [
                'fieldFake' => 'OK',
            ],
        ]);
})
    ->with('schema')
    ->with('query');

it('bails out when header is present', function (string $schema, string $query) {
    $this->schema = $schema;
    $this->mockResolver()->willReturn('OK');

    /** @noinspection GraphQLUnresolvedReference */
    $this->graphQL($query, headers: [config('header') => ''])
        ->assertExactJson([
            'data' => [
                'fieldFake' => 'OK',
            ],
        ]);
})
    ->with('schema')
    ->with('query');

it('bails out when query is introspection query', function (string $schema, string $query) {
    $this->schema = $schema;

    /** @noinspection GraphQLUnresolvedReference */
    $this->graphQL($query)->assertGraphQLErrorFree();
})
    ->with('schema')
    ->with('introspection query');

it('aborts when header is missing', function (string $schema, string $query) {
    $this->schema = $schema;
    $this->mockResolverExpects($this->never());

    /** @noinspection GraphQLUnresolvedReference */
    $this->graphQL($query)
        ->assertGraphQLErrorMessage('Internal Server Error');
})
    ->with('schema')
    ->with('query');

it('aborts using custom response factory when available', function (string $schema, string $query) {
    $this->schema = $schema;
    $this->mockResolverExpects($this->never());
    Honeypot::respondToFormTokenErrorsUsing(fn () => response(['data' => 'response fake']));

    /** @noinspection GraphQLUnresolvedReference */
    $this->graphQL($query)
        ->assertExactJson(['data' => 'response fake']);
})
    ->with('schema')
    ->with('query');
