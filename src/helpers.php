<?php

namespace Hettiger\Honeypot;

/**
 * Returns a service of the given `$type` from the container
 *
 * @template T
 *
 * @param  T  $type
 * @param  array  $parameters
 * @return T | mixed
 */
function resolveByType(mixed $type, array $parameters = []): mixed
{
    return app($type, $parameters);
}
