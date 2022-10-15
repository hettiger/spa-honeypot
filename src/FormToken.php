<?php

namespace Hettiger\Honeypot;

use DateInterval;
use Hettiger\Honeypot\Contracts\Cache;
use Hettiger\Honeypot\Contracts\TimeSource;
use Hettiger\Honeypot\Contracts\UuidGenerator;

class FormToken
{
    public readonly string $id;

    public static function make(): FormToken
    {
        return resolve(FormToken::class);
    }

    public static function fromId(string $id): FormToken
    {
        return resolve(FormToken::class, [
            'id' => $id,
        ]);
    }

    public function __construct(
        protected TimeSource $timeSource,
        protected UuidGenerator $uuidGenerator,
        protected Cache $cache,
        protected array $config,
        ?string $id = null,
    ) {
        $this->id = $id ?? $this->uuidGenerator->uuid();
    }

    public function persisted(): static
    {
        $this->cache->put(
            $this->id,
            $this->timeSource->now(),
            new DateInterval('PT15M'),
        );

        return $this;
    }

    /**
     * Returns whether the token is valid or not
     *
     * A token can only be used once. => Invalidation is part of the validation.
     * The token becomes invalid when we pull it from the cache.
     */
    public function isValid(): bool
    {
        $age = $this->cache->pull($this->id);
        $minAge = (int) $this->config['min_age'];

        return $age !== null && $age + $minAge < $this->timeSource->now();
    }
}
