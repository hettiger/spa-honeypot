<?php

namespace Hettiger\Honeypot\Tests\Features\HoneypotProtection;

use function Hettiger\Honeypot\config;
use Illuminate\Support\Facades\Route;
use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpFoundation\Response;

beforeEach(function () {
    Route::post('route-fake', fn () => 'OK')
        ->middleware('form.honeypot')
        ->name('fake');
});

function attempt(?string $value = null)
{
    $data = $value ? [config('field') => $value] : [];

    return test()->post(route('fake'), $data);
}

function assertDidAccept(TestResponse $response)
{
    $response->assertOk();
}

function assertDidBlock(TestResponse $response)
{
    $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
}

it('accepts requests with missing honeypot field', function () {
    assertDidAccept(attempt());
});

it('accepts requests with empty honeypot field', function () {
    assertDidAccept(attempt(''));
});

it('blocks request with filled honeypot field', function () {
    assertDidBlock(attempt('value-fake'));
});
