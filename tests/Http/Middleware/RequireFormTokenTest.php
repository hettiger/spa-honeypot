<?php

use Hettiger\Honeypot\Http\Middleware\RequireFormToken;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

it('bails out when header is present', function (array $config) {
    $sut = resolveByType(RequireFormToken::class);

    $request = new Request();
    $request->headers->set($config['header'], '');

    $response = $sut->handle($request, fn () => 'bailed out');

    expect($response)->toEqual('bailed out');
})->with('config');

it('aborts when header is missing', function () {
    $sut = resolveByType(RequireFormToken::class);

    $request = new Request();

    expect(fn () => $sut->handle($request, fn () => 'bailed out'))
        ->toAbortWith(Response::HTTP_INTERNAL_SERVER_ERROR);
});
