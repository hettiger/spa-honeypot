<?php

namespace Hettiger\Honeypot\GraphQL\Scalars;

use GraphQL\Error\Error;
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
     * @return mixed
     */
    public function serialize($value)
    {
        throw new Error('Serializing honeypot is not supported.');
    }

    /**
     * Parses an externally provided value (query variable) to use as an input
     *
     * @param  mixed  $value
     * @return mixed
     */
    public function parseValue($value)
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
     * @param  \GraphQL\Language\AST\Node  $valueNode
     * @param  array<string, mixed>|null  $variables
     * @return mixed
     */
    public function parseLiteral($valueNode, ?array $variables = null)
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
