<?php

namespace Hettiger\Honeypot\Tests\Features\FormTokenProtection;

use function Hettiger\Honeypot\config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use function Pest\Laravel\travel;
use function Pest\Laravel\travelBack;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Response;

beforeEach(function () {
    Str::createUuidsUsingSequence(
        [Uuid::fromString('FEE116E5-5734-4372-85E7-3BED7D3AAED7')],
        fn () => Uuid::fromString('F5DFC652-190C-47C6-A1D7-55F58C9B7448')
    );

    Route::post('route-fake', fn () => 'OK')
        ->middleware('form.token')
        ->name('fake');
});

afterEach(function () {
    Str::createUuidsNormally();
    travelBack();
});

function token(): string
{
    $header = config('header');

    return test()->post(route('fake'), headers: [$header => ''])->headers->get($header);
}

function simulateSlowHuman()
{
    travel(config('min_age')->totalSeconds)->seconds();
}

function attempt(?string $token = null): TestResponse
{
    $headers = $token ? [config('header') => $token] : [];

    return test()->post(route('fake'), headers: $headers);
}

function assertDidAccept(TestResponse $response)
{
    assertHeaderIsPresent($response)->assertOk();
}

function assertDidBlock(TestResponse $response, $withHeader = true)
{
    ($withHeader ? assertHeaderIsPresent($response) : assertHeaderIsMissing($response))
        ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
}

function assertHeaderIsPresent(TestResponse $response): TestResponse
{
    return $response->assertHeader(config('header'), Str::uuid()->toString());
}

function assertHeaderIsMissing(TestResponse $response): TestResponse
{
    return $response->assertHeaderMissing(config('header'));
}

it('accepts slow humans with valid tokens', function () {
    $validToken = token();
    simulateSlowHuman();

    assertDidAccept(attempt($validToken));
});

it('blocks invalid tokens', function () {
    $invalidToken = Str::uuid()->toString();
    simulateSlowHuman();

    assertDidBlock(attempt($invalidToken));
});

it('blocks missing tokens', function () {
    simulateSlowHuman();

    assertDidBlock(attempt(), false);
});

it('blocks fast bots', function () {
    $validToken = token();

    assertDidBlock(attempt($validToken));
});
