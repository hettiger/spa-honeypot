<?php

/** @noinspection GraphQLUnresolvedReference */

dataset('schema', fn () => [
    'schema' => /** @lang GraphQL */ <<<'GRAPHQL'
type Query {
    fieldFake: String @requireFormToken @mock
}
GRAPHQL
]);
