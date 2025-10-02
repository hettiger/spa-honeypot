<?php

namespace Hettiger\Honeypot;

use Illuminate\Config\Repository;

/**
 * Returns a service of the given `$type` from the container
 *
 * @template T
 *
 * @param  T  $type
 * @return T | mixed
 */
function resolveByType(mixed $type, array $parameters = []): mixed
{
    return app($type, $parameters);
}

/**
 * Get / set the specified configuration value.
 *
 * If a string is passed as the key, we prepend with `'spa-honeypot.'` when appropriate.
 * If an array is passed as the key, we will assume you want to set an array of values.
 *
 * @return mixed|Repository
 */
function config(array|null|string $key = null, mixed $default = null): mixed
{
    return \config(
        $key,
        \config(
            is_string($key)
                ? (string) str($key)->unless(
                    fn ($s) => $s->contains('.'),
                    fn ($s) => $s->prepend('spa-honeypot.'),
                )
                : $key,
            $default
        )
    );
}
