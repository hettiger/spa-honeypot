<?php

use Illuminate\Support\Facades\Route;

beforeEach(function () {
    Route::post('route-fake', fn () => 'OK')
        ->middleware('form.honeypot')
        ->name('fake');
});

it('accepts requests with missing honeypot field', function () {
    $this->assertDidAccept($this->attempt(), false);
});

it('accepts requests with empty honeypot field', function () {
    $this->assertDidAccept($this->attempt(value: ''), false);
});

it('blocks request with filled honeypot field', function () {
    $this->assertDidBlock($this->attempt(value: 'value-fake'), false);
});
