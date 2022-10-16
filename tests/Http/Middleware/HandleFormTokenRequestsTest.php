<?php

use Hettiger\Honeypot\FormToken;
use Hettiger\Honeypot\Http\Middleware\HandleFormTokenRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use function Pest\Laravel\travel;
use Symfony\Component\HttpFoundation\Response;

beforeEach(function () {
    Str::freezeUuids();
});

it('bails out when header is missing', function () {
    $sut = resolveByType(HandleFormTokenRequests::class);

    $response = $sut->handle(new Request(), fn () => 'bailed out');

    expect($response)->toEqual('bailed out');
});

it('aborts when an invalid or empty token is present in the header', function (array $config, string $token) {
    $sut = resolveByType(HandleFormTokenRequests::class);

    $request = new Request();
    $request->headers->set($config['header'], $token);

    expect(fn () => $sut->handle($request, fn () => 'bailed out'))
        ->toAbortWith(
            Response::HTTP_INTERNAL_SERVER_ERROR,
            headers: [$config['header'] => Str::uuid()->toString()]
        );
})
->with('config')
->with([
    'empty token' => fn () => '',
    'invalid token' => fn () => 'invalid-token',
    'valid token' => fn () => FormToken::make()->persisted()->id,
]);

it('bails out when a valid token is present in the header', function (array $config) {
    $sut = resolveByType(HandleFormTokenRequests::class);

    $request = new Request();
    $request->headers->set($config['header'], FormToken::make()->persisted()->id);
    travel($config['min_age']->totalSeconds)->seconds();

    $response = $sut->handle($request, fn () => 'bailed out');

    expect($response->getContent())
        ->toEqual('bailed out')
        ->and($response->headers->contains($config['header'], Str::uuid()->toString()))
        ->toBeTrue();
})
->with('config');
