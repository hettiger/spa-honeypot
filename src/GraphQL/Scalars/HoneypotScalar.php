<?php

namespace Hettiger\Honeypot\GraphQL\Scalars;

use GraphQL\Type\Definition\ScalarType;
use Hettiger\Honeypot\Facades\Honeypot;

/**
 * TODO: Needs tests …
 *
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
        // Assuming the internal representation of the value is always correct
        return $value;
    }

    /**
     * Parses an externally provided value (query variable) to use as an input
     *
     * @param  mixed  $value
     * @return mixed
     */
    public function parseValue($value)
    {
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
        abort_unless(
            empty($valueNode->value),
            Honeypot::honeypotErrorResponse(),
        );

        return $valueNode->value;
    }
}
