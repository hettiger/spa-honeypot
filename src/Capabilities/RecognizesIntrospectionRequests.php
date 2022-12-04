<?php

namespace Hettiger\Honeypot\Capabilities;

use GraphQL\Error\SyntaxError;
use GraphQL\Language\AST\NodeKind;
use GraphQL\Language\Parser;
use GraphQL\Language\Token;

trait RecognizesIntrospectionRequests
{
    use RecognizesGraphQLRequests;

    protected function isIntrospectionRequest(): bool
    {
        $isIntrospectionQuery = function (Token|null $token) use (&$isIntrospectionQuery): bool {
            if (! $token) {
                return false;
            }

            if ($token->kind === NodeKind::NAME && in_array($token->value, ['__type', '__schema'])) {
                return true;
            }

            return $isIntrospectionQuery($token->next);
        };

        try {
            $ast = Parser::parse(request('query'));
        } catch (SyntaxError) {
            $ast = null;
        }

        return $this->isGraphQLRequest() && $isIntrospectionQuery($ast?->loc?->startToken);
    }
}
