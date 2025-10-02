<?php

namespace Hettiger\Honeypot\GraphQL\Scalars;

use GraphQL\Error\Error;
use GraphQL\Language\AST\Node;
use GraphQL\Type\Definition\ScalarType;
use Hettiger\Honeypot\Facades\Honeypot;

use function Hettiger\Honeypot\config;

/**
 * Read more about scalars here https://webonyx.github.io/graphql-php/type-definitions/scalars
 */
final class HoneypotScalar extends ScalarType
{
    /**
     * Serializes an internal value to include in a response.
     *
     * @param  mixed  $value
     *
     * @throws Error
     */
    public function serialize($value): mixed
    {
        throw new Error('Serializing honeypot is not supported.');
    }

    /**
     * Parses an externally provided value (query variable) to use as an input
     *
     * @param  mixed  $value
     */
    public function parseValue($value): mixed
    {
        if (! config('enabled')) {
            return $value;
        }

        abort_unless(
            empty($value),
            Honeypot::honeypotErrorResponse(),
        );

        return $value;
    }

    /**
     * Parses an externally provided literal value (hardcoded in GraphQL query) to use as an input.
     *
     * E.g.
     * {
     *   user(email: "user@example.com")
     * }
     *
     * @param  array<string, mixed>|null  $variables
     *
     * @throws Error
     */
    public function parseLiteral(Node $valueNode, ?array $variables = null): mixed
    {
        if (! property_exists($valueNode, 'value')) {
            throw new Error('Type of $valueNode does not provide a value property.');
        }

        if (! config('enabled')) {
            return $valueNode->value;
        }

        abort_unless(
            empty($valueNode->value),
            Honeypot::honeypotErrorResponse(),
        );

        return $valueNode->value;
    }
}
