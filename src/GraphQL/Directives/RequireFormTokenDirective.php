<?php

namespace Hettiger\Honeypot\GraphQL\Directives;

use Closure;
use Nuwave\Lighthouse\Schema\Directives\BaseDirective;
use Nuwave\Lighthouse\Schema\Values\TypeValue;
use Nuwave\Lighthouse\Support\Contracts\TypeMiddleware;

final class RequireFormTokenDirective extends BaseDirective implements TypeMiddleware
{
    // TODO implement the directive https://lighthouse-php.com/master/custom-directives/getting-started.html

    public static function definition(): string
    {
        return /** @lang GraphQL */ <<<'GRAPHQL'
directive @requireFormToken on INPUT_OBJECT
GRAPHQL;
    }

    /**
     * Handle a type AST as it is converted to an executable type.
     *
     * @param  \Nuwave\Lighthouse\Schema\Values\TypeValue  $value
     * @param  \Closure  $next
     * @return \GraphQL\Type\Definition\Type
     */
    public function handleNode(TypeValue $value, Closure $next)
    {
        return $next($value);
    }
}
