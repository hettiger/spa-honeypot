<?php

namespace Hettiger\Honeypot;

/**
 * Returns a service of the given `$type` from the container
 *
 * @template T
 * @param T $type
 * @param array $parameters
 * @return T
 */
function resolveByType(string $type, array $parameters = [])
{
    return app($type, $parameters);
}
