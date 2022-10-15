<?php

use Carbon\CarbonInterval;
use Hettiger\Honeypot\FormToken;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

function withValidTime(int $cachedTokenTime)
{
    freezeTimestamp($cachedTokenTime + config('spa-honeypot.min_age') + 1);
}

function withCachedToken(string $id, int $timestamp)
{
    Cache::put($id, $timestamp);
}

it('can be instantiated using a factory function', function () {
    Str::freezeUuids();

    $token = FormToken::make();

    expect($token)
        ->toBeInstanceOf(FormToken::class)
        ->and($token->id)
        ->toEqual(Str::uuid()->toString());
});

it('can be instantiated using an existing ID', function () {
    $token = FormToken::fromId('id-fake');

    expect($token->id)->toEqual('id-fake');
});

it('can be stored in the cache for future validation', function () {
    Str::freezeUuids();
    freezeTimestamp(1337);

    Cache::shouldReceive('put')->withArgs(
        fn (string $key, int $value, CarbonInterval $ttl) => $key === Str::uuid()->toString()
            && $value === 1337
            && now()->add($ttl)->equalTo(now()->add(CarbonInterval::minutes(15)))
    )->once();

    $token = FormToken::make();

    expect($token->persisted())->toBeInstanceOf(FormToken::class);
});

it('fails validation when it is not present in the cache', function () {
    $token = FormToken::fromId('uuid-fake');

    expect($token->isValid())->toBeFalse();
});

it('fails validation when it is not old enough', function () {
    foreach (range(0, config('spa-honeypot.min_age')) as $age) {
        freezeTimestamp(1337 + $age);
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
