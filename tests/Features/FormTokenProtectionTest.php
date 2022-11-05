<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use function Pest\Laravel\travelBack;
use Ramsey\Uuid\Uuid;

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

it('accepts slow humans with valid tokens', function () {
    $validToken = $this->token();
    simulateSlowHuman();

    $this->assertDidAccept($this->attempt($validToken));
});

it('blocks invalid tokens', function () {
    $invalidToken = Str::uuid()->toString();
    simulateSlowHuman();

    $this->assertDidBlock($this->attempt($invalidToken));
});

it('blocks missing tokens', function () {
    simulateSlowHuman();

    $this->assertDidBlock($this->attempt(), false);
});

it('blocks fast bots', function () {
    $validToken = $this->token();

    $this->assertDidBlock($this->attempt($validToken));
});
