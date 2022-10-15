<?php

namespace Hettiger\Honeypot;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

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
        protected array $config,
        ?string $id = null,
    ) {
        $this->id = $id ?? Str::uuid();
    }

    public function persisted(): static
    {
        Cache::put(
            $this->id,
            now()->getTimestamp(),
            $this->config['max_age'],
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
        /** @var ?int $createdAt */
        $createdAt = Cache::pull($this->id);
        $minAge = $this->config['min_age'];

        return $createdAt !== null && Carbon::createFromTimestamp($createdAt)->add($minAge)->lessThan(now());
    }
}
