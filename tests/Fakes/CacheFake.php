<?php

namespace Hettiger\Honeypot\Tests\Fakes;

use DateInterval;
use DateTime;
use Hettiger\Honeypot\Contracts\Cache;
use Hettiger\Honeypot\Contracts\TimeSource;
use Illuminate\Support\Collection;

class CacheItem
{
    public function __construct(
        public mixed $value,
        public ?DateTime $expiresAt,
    ) {
    }
}

class CacheFake implements Cache
{
    /**
     * @var CacheItem[]
     */
    protected static array $store = [];

    public static function store(): Collection
    {
        return collect(static::$store);
    }

    public static function clear()
    {
        static::$store = [];
    }

    public function __construct(
        protected TimeSource $timeSource
    ) {
    }

    public function put($key, $value, $ttl = null)
    {
        $expiresAt = $ttl;

        if (is_int($ttl)) {
            $expiresAt = $this->now()->add(new DateInterval("PT{$ttl}S"));
        }

        if ($ttl instanceof DateInterval) {
            $expiresAt = $this->now()->add($ttl);
        }

        static::$store[$key] = new CacheItem($value, $expiresAt);
    }

    public function pull($key, $default = null)
    {
        if (isset(static::$store[$key])) {
            $item = static::$store[$key];
            unset(static::$store[$key]);

            if ($item->expiresAt && $item->expiresAt <= $this->now()) {
                return null;
            }

            return $item->value;
        }

        return null;
    }

    protected function now(): DateTime
    {
        return (new DateTime())->setTimestamp($this->timeSource->now());
    }
}
