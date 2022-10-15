<?php

use Hettiger\Honeypot\Contracts\Cache;
use Hettiger\Honeypot\FormToken;
use Hettiger\Honeypot\Tests\Fakes\CacheFake;
use function Pest\Laravel\swap;

beforeEach(fn () => CacheFake::clear());

function withValidTime(int $cachedTokenTime)
{
    withTime($cachedTokenTime + config('spa-honeypot.min_age') + 1);
}

function withCachedToken(string $id, int $time)
{
    $cache = resolveByType(Cache::class);
    $cache->put($id, $time);
}

it('can be instantiated using a factory function', function () {
    withUuid('uuid-fake');

    $token = FormToken::make();

    expect($token)
        ->toBeInstanceOf(FormToken::class)
        ->and($token->id)
        ->toEqual('uuid-fake');
});

it('can be instantiated using an existing ID', function () {
    $token = FormToken::fromId('id-fake');

    expect($token->id)->toEqual('id-fake');
});

it('can be stored in the cache for future validation', function () {
    withTime(1337);
    withUuid('uuid-fake');

    swap(Cache::class, mock(Cache::class)->expect(
        put: fn ($key, $value, $ttl) => expect([$key, $value, $ttl])->toEqual([
            'uuid-fake',
            1337,
            new DateInterval('PT15M'),
        ]),
    ));

    $token = FormToken::make();

    expect($token->persisted())->toBeInstanceOf(FormToken::class);
});

it('fails validation when it is not present in the cache', function () {
    $token = FormToken::fromId('uuid-fake');

    expect($token->isValid())->toBeFalse();
});

it('fails validation when it is not old enough', function () {
    foreach (range(0, config('spa-honeypot.min_age')) as $age) {
        withTime(1337 + $age);
        withCachedToken('uuid-fake', 1337);

        $token = FormToken::fromId('uuid-fake');

        expect($token->isValid())->toBeFalse();
    }
});

it('passes validation when it is old enough', function () {
    withCachedToken('uuid-fake', 1337);
    withValidTime(1337);

    $token = FormToken::fromId('uuid-fake');

    expect($token->isValid())->toBeTrue();
});

it('fails validation on subsequent calls', function () {
    withCachedToken('uuid-fake', 1337);
    withValidTime(1337);

    $token = FormToken::fromId('uuid-fake');

    expect($token->isValid())
        ->toBeTrue()
        ->and($token->isValid())
        ->toBeFalse();
});
