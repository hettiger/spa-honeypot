<?php

use Carbon\CarbonInterval;
use function Hettiger\Honeypot\config;
use Hettiger\Honeypot\FormToken;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use function Pest\Laravel\travel;
use function Pest\Laravel\travelBack;
use function Pest\Laravel\travelTo;

beforeEach(function () {
    Str::freezeUuids();
    travelTo(today());
});

afterEach(function () {
    Str::createUuidsNormally();
    travelBack();
});

it('can be instantiated using a factory function', function () {
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
    Cache::shouldReceive('put')->withArgs(
        fn (string $key, int $value, CarbonInterval $ttl) => $key === config('cache_prefix').Str::uuid()->toString()
            && $value === now()->timestamp
            && now()->add($ttl)->equalTo(now()->add(config('max_age')))
    )->once();

    $token = FormToken::make();

    expect($token->persisted())->toBeInstanceOf(FormToken::class);
});

it('fails validation when it is not present in the cache', function () {
    $token = FormToken::fromId('uuid-fake');

    expect($token->isValid())->toBeFalse();
});

it('fails validation when it is not old enough', function () {
    foreach (range(0, config('min_age')->totalSeconds) as $age) {
        $token = FormToken::make()->persisted();

        travel($age)->seconds();

        expect($token->isValid())->toBeFalse();
    }
});

it('passes validation when it is old enough', function () {
    $token = FormToken::make()->persisted();

    travel(config('min_age')->totalSeconds + 1)->seconds();

    expect($token->isValid())->toBeTrue();
});

it('fails validation on subsequent calls', function () {
    $token = FormToken::make()->persisted();

    travel(config('min_age')->totalSeconds + 1)->seconds();

    expect($token->isValid())
        ->toBeTrue()
        ->and($token->isValid())
        ->toBeFalse();
});

test('`isValid()` does not read from cache when ID is not a valid UUID', function () {
    $token = FormToken::fromId('invalid-id');

    Cache::shouldReceive('pull')->never();

    expect($token->isValid())->toBeFalse();
});
