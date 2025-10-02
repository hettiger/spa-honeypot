<?php

namespace Hettiger\Honeypot;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class FormToken
{
    /**
     * The token ID
     *
     * A valid token ID is a UUID, and we could type this as an `UuidInterface`.
     * However, we consider any input as a `FormToken` that can be validated.
     * Therefore, it's necessary to use a more generous type here …
     */
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
        ?string $id = null,
    ) {
        $this->id = $id ?? Str::uuid();
    }

    public function persisted(): static
    {
        Cache::put(
            $this->cacheKey(),
            now()->getTimestamp(),
            config('max_age'),
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
        if (! Uuid::isValid($this->id)) {
            return false;
        }

        /** @var ?int $createdAt */
        $createdAt = Cache::pull($this->cacheKey());
        $minAge = config('min_age');

        return $createdAt !== null && Carbon::createFromTimestamp($createdAt)->add($minAge)->lessThan(now());
    }

    protected function cacheKey(): string
    {
        return config('cache_prefix').$this->id;
    }
}
