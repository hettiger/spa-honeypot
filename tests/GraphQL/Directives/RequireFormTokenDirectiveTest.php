<?php

use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;
use Nuwave\Lighthouse\Testing\MocksResolvers;
use Nuwave\Lighthouse\Testing\UsesTestSchema;

uses(
    MakesGraphQLRequests::class,
    UsesTestSchema::class,
    MocksResolvers::class,
);

beforeEach(function () {
    $this->setUpTestSchema();
});

it('bails out when header is present', function (array $config, string $schema, string $query) {
    $this->schema = $schema;
    $this->mockResolver()->willReturn('OK');

    /** @noinspection GraphQLUnresolvedReference */
    $this->graphQL($query, headers: [$config['header'] => ''])
        ->assertExactJson([
            'data' => [
                'fieldFake' => 'OK',
            ],
        ]);
})
->with('config')
->with('schema')
->with('query');

it('throws when header is missing', function (string $schema, string $query) {
    $this->schema = $schema;
    $this->mockResolverExpects($this->never());

    /** @noinspection GraphQLUnresolvedReference */
    $this->graphQL($query)
        ->assertGraphQLErrorMessage('Internal Server Error');
})
->with('schema')
->with('query');
