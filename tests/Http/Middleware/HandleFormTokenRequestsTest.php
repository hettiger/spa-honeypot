<?php

use Hettiger\Honeypot\Http\Middleware\HandleFormTokenRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

beforeEach(function () {
    Str::freezeUuids();
});

it('bails out when header is missing', function () {
    $sut = resolveByType(HandleFormTokenRequests::class);

    $response = $sut->handle(new Request(), fn () => 'bailed out');

    expect($response)->toEqual('bailed out');
});

it('responds with a form token when header is present but empty', function (array $config) {
    $sut = resolveByType(HandleFormTokenRequests::class);

    $request = new Request();
    $request->headers->set($config['header'], '');

    $response = $sut->handle($request, fn () => 'bailed out');

    expect($response)->toEqual(Str::uuid()->toString());
})->with('config');

it('bails out when header is present and not empty', function (array $config) {
    $sut = resolveByType(HandleFormTokenRequests::class);

    $request = new Request();
    $request->headers->set($config['header'], 'not empty');

    $response = $sut->handle($request, fn () => 'bailed out');

    expect($response)->toEqual('bailed out');
})->with('config');
