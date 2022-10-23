<?php

namespace Hettiger\Honeypot\GraphQL\Directives;

use Closure;
use Hettiger\Honeypot\Capabilities\RecognizesFormTokenRequests;
use Hettiger\Honeypot\Facades\Honeypot;
use Nuwave\Lighthouse\Schema\Directives\BaseDirective;
use Nuwave\Lighthouse\Schema\Values\FieldValue;
use Nuwave\Lighthouse\Support\Contracts\FieldMiddleware;

final class RequireFormTokenDirective extends BaseDirective implements FieldMiddleware
{
    use RecognizesFormTokenRequests;

    public static function definition(): string
    {
        return /** @lang GraphQL */ <<<'GRAPHQL'
directive @requireFormToken on FIELD_DEFINITION
GRAPHQL;
    }

    /**
     * Wrap around the final field resolver.
     *
     * @param  \Nuwave\Lighthouse\Schema\Values\FieldValue  $fieldValue
     * @param  \Closure  $next
     * @return \Nuwave\Lighthouse\Schema\Values\FieldValue
     */
    public function handleField(FieldValue $fieldValue, Closure $next)
    {
        abort_unless(
            $this->isFormTokenRequest(),
            Honeypot::formTokenErrorResponse(false),
        );

        return $next($fieldValue);
    }
}
