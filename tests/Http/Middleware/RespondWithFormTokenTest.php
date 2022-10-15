<?php

use Hettiger\Honeypot\Http\Middleware\RespondWithFormToken;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

beforeEach(function () {
    Str::freezeUuids();
});

it('bails out when form token header is missing', function () {
    $sut = resolveByType(RespondWithFormToken::class);

    $response = $sut->handle(new Request(), fn () => 'bailed out');

    expect($response)->toEqual('bailed out');
});

it('responds with a form token when form token header is present but empty', function (array $config) {
    $sut = resolveByType(RespondWithFormToken::class);

    $request = new Request();
    $request->headers->set($config['form_token_header'], '');

    $response = $sut->handle($request, fn () => 'bailed out');

    expect($response)->toEqual(Str::uuid()->toString());
})->with('config');

it('bails out when form token header is present and not empty', function (array $config) {
    $sut = resolveByType(RespondWithFormToken::class);

    $request = new Request();
    $request->headers->set($config['form_token_header'], 'not empty');

    $response = $sut->handle($request, fn () => 'bailed out');

    expect($response)->toEqual('bailed out');
})->with('config');
