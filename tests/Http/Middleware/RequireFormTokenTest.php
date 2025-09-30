<?php

use Hettiger\Honeypot\Facades\Honeypot;
use Hettiger\Honeypot\Http\Middleware\RequireFormToken;
use Symfony\Component\HttpFoundation\Response;

use function Hettiger\Honeypot\config;
use function Hettiger\Honeypot\resolveByType;

it('bails out when package is not enabled', function () {
    config()->set('spa-honeypot.enabled', false);
    $sut = resolveByType(RequireFormToken::class);
    $request = withRequest();

    $response = $sut->handle($request, fn () => 'bailed out');

    expect($response)->toEqual('bailed out');
});

it('bails out when header is present', function () {
    $sut = resolveByType(RequireFormToken::class);
    $request = withRequest();
    $request->headers->set(config('header'), '');

    $response = $sut->handle($request, fn () => 'bailed out');

    expect($response)->toEqual('bailed out');
});

it('aborts when header is missing', function () {
    $sut = resolveByType(RequireFormToken::class);
    $request = withRequest();

    expect(fn () => $sut->handle($request, fn () => 'bailed out'))
        ->toAbortWith(Response::HTTP_INTERNAL_SERVER_ERROR);
});

it('aborts using custom response factory when available', function () {
    $sut = resolveByType(RequireFormToken::class);
    $request = withRequest();
    $expectedResponse = response('response fake');
    Honeypot::respondToFormTokenErrorsUsing(fn () => $expectedResponse);

    expect(fn () => $sut->handle($request, fn () => 'bailed out'))
        ->toAbortWithResponse($expectedResponse);
});
