<?php

use function Hettiger\Honeypot\config;
use Hettiger\Honeypot\Http\Middleware\AbortWhenHoneypotIsFilled;
use function Hettiger\Honeypot\resolveByType;
use Symfony\Component\HttpFoundation\Response;

it('bails out when honeypot is missing', function () {
    $sut = resolveByType(AbortWhenHoneypotIsFilled::class);
    $request = withRequest();

    $response = $sut->handle($request, fn () => 'bailed out');

    expect($response)->toEqual('bailed out');
});

it('bails out when honeypot is empty', function () {
    $sut = resolveByType(AbortWhenHoneypotIsFilled::class);
    $request = withRequest();
    $request->merge([
        config('field') => '',
    ]);

    $response = $sut->handle($request, fn () => 'bailed out');

    expect($response)->toEqual('bailed out');
});

it('aborts when honeypot is filled', function () {
    $sut = resolveByType(AbortWhenHoneypotIsFilled::class);
    $request = withRequest();
    $request->merge([
        config('field') => 'value',
    ]);

    expect(fn () => $sut->handle($request, fn () => 'bailed out'))
        ->toAbortWith(Response::HTTP_INTERNAL_SERVER_ERROR);
});

it('works with nested fields', function () {
    config([
        'spa-honeypot' => [
            'field' => 'nested.honey',
        ],
    ]);
    $sut = resolveByType(AbortWhenHoneypotIsFilled::class);
    $request = withRequest();
    $request->merge([
        'nested' => [
            'honey' => 'value',
        ],
    ]);

    expect(fn () => $sut->handle($request, fn () => 'bailed out'))
        ->toAbortWith(Response::HTTP_INTERNAL_SERVER_ERROR);
});
