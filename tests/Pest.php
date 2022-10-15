<?php

use Hettiger\Honeypot\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

/**
 * Returns a service of the given `$type` from the container
 *
 * @template T
 *
 * @param  T  $type
 * @param  array  $parameters
 * @return T
 */
function resolveByType(mixed $type, array $parameters = [])
{
    return app($type, $parameters);
}
