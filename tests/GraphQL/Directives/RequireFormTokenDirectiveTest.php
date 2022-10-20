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

    /** @noinspection GraphQLUnresolvedReference */
    $this->schema = /** @lang GraphQL */ <<<'GRAPHQL'
type Query {
    fieldFake: String @requireFormToken @mock
}
GRAPHQL;
});

it('bails out when header is present', function (array $config) {
    $this->mockResolver()->willReturn('OK');

    /** @noinspection GraphQLUnresolvedReference */
    $this->graphQL(
/** @lang GraphQL */ <<<'GRAPHQL'
{
    fieldFake
}
GRAPHQL,
        headers: [$config['header'] => '']
    )->assertExactJson([
        'data' => [
            'fieldFake' => 'OK',
        ],
    ]);
})->with('config');

it('throws when header is missing', function () {
    $this->mockResolverExpects($this->never());

    /** @noinspection GraphQLUnresolvedReference */
    $this->graphQL(/** @lang GraphQL */ <<<'GRAPHQL'
{
    fieldFake
}
GRAPHQL)->assertGraphQLErrorMessage('Internal Server Error');
});
