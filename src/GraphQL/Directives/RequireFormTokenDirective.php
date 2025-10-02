<?php

namespace Hettiger\Honeypot\GraphQL\Directives;

use Hettiger\Honeypot\Capabilities\RecognizesFormTokenRequests;
use Hettiger\Honeypot\Capabilities\RecognizesIntrospectionRequests;
use Hettiger\Honeypot\Facades\Honeypot;
use Hettiger\Honeypot\GraphQL\Exceptions\ClientSafeHttpResponseException;
use Nuwave\Lighthouse\Schema\Directives\BaseDirective;
use Nuwave\Lighthouse\Schema\Values\FieldValue;
use Nuwave\Lighthouse\Support\Contracts\FieldMiddleware;

use function Hettiger\Honeypot\config;

final class RequireFormTokenDirective extends BaseDirective implements FieldMiddleware
{
    use RecognizesFormTokenRequests;
    use RecognizesIntrospectionRequests;

    public static function definition(): string
    {
        return /** @lang GraphQL */ <<<'GRAPHQL'
directive @requireFormToken on FIELD_DEFINITION
GRAPHQL;
    }

    /**
     * Wrap around the final field resolver.
     */
    public function handleField(FieldValue $fieldValue): void
    {
        if (! config('enabled') || ! $this->isGraphQLRequest()) {
            return;
        }

        if ($this->isFormTokenRequest() || $this->isIntrospectionRequest()) {
            return;
        }

        throw new ClientSafeHttpResponseException(Honeypot::formTokenErrorResponse(false));
    }
}
