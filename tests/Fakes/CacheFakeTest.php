<?php

use Hettiger\Honeypot\Tests\Fakes\CacheFake;

beforeEach(fn () => CacheFake::clear());

test('put stores an item', function () {
    $cache = resolveByType(CacheFake::class);

    $cache->put('key-fake', 'value-fake');

    expect($cache->store()->has('key-fake'))->toBeTrue();
});

test('pull returns null if item does not exist', function () {
    $cache = resolveByType(CacheFake::class);

    expect($cache->pull('key-fake'))->toBeNull();
});

test("pull removes item from the store and returns it's value", function ($ttl) {
    if (! is_null($ttl)) {
        withTime(1337);
    }

    $cache = resolveByType(CacheFake::class);

    $cache->put('key-fake', 'value-fake', $ttl);

    expect($cache->store()->has('key-fake'))
        ->toBeTrue()
        ->and($cache->pull('key-fake'))
        ->toEqual('value-fake')
        ->and($cache->store()->isEmpty())
        ->toBeTrue()
        ->and($cache->pull('key-fake'))
        ->toBeNull();
})->with([
    'ttl: null' => null,
    'ttl: int' => 10,
    'ttl: DateTime' => new DateTime(),
    'ttl: DateInterval' => new DateInterval('PT10S'),
]);

test('pull removes expired item from the store and returns null', function ($ttl) {
    if (! ($ttl instanceof DateTime)) {
        withTime(1337);
    }
    $cache = resolveByType(CacheFake::class);

    $cache->put('key-fake', 'value-fake', $ttl);

    withTime(1337 + 10);
    $cache = resolveByType(CacheFake::class);

    expect($cache->pull('key-fake'))
        ->toBeNull()
        ->and($cache->store()->isEmpty())
        ->toBeTrue();
})->with([
    'ttl: int' => 10,
    'ttl: DateTime' => (new DateTime())->setTimestamp(1337 + 10),
    'ttl: DateInterval' => new DateInterval('PT10S'),
]);
