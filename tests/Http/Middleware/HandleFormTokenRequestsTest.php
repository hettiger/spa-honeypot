<?php

use Hettiger\Honeypot\FormToken;
use Hettiger\Honeypot\Http\Middleware\HandleFormTokenRequests;
use function Hettiger\Honeypot\resolveByType;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use function Pest\Laravel\travel;
use Symfony\Component\HttpFoundation\Response;

beforeEach(function () {
    Str::freezeUuids();
});

it('bails out when header is missing', function () {
    $sut = resolveByType(HandleFormTokenRequests::class);

    $response = $sut->handle(makeRequest(), fn () => 'bailed out');

    expect($response)->toEqual('bailed out');
});

it('aborts when an invalid or empty token is present in the header', function (array $config, string $token) {
    $sut = resolveByType(HandleFormTokenRequests::class);

    $request = makeRequest();
    $request->headers->set($config['header'], $token);

    expect(fn () => $sut->handle($request, fn () => 'bailed out'))
        ->toAbortWith(
            Response::HTTP_INTERNAL_SERVER_ERROR,
            headers: [$config['header'] => Str::uuid()->toString()]
        );
})
->with('config')
->with('tokens');

it('throws GraphQL spec conforming errors on GraphQL requests', function (array $config, string $token) {
    $sut = resolveByType(HandleFormTokenRequests::class);

    $request = makeRequest();
    $request->setRouteResolver(fn () => Route::getRoutes()->getByName('graphql'));
    $request->headers->set($config['header'], $token);

    expect(fn () => $sut->handle($request, fn () => 'bailed out'))
        ->toThrow(fn (HttpResponseException $exception) => expect($exception->getResponse()->getContent())
            ->toEqual(json_encode(['errors' => ['message' => 'Internal Server Error']]))
            ->and($exception->getResponse()->headers->contains($config['header'], Str::uuid()->toString()))
        );
})
->with('config')
->with('tokens');

it('bails out when a valid token is present in the header', function (array $config) {
    $sut = resolveByType(HandleFormTokenRequests::class);

    $request = makeRequest();
    $request->headers->set($config['header'], FormToken::make()->persisted()->id);
    travel($config['min_age']->totalSeconds)->seconds();

    $response = $sut->handle($request, fn () => 'bailed out');

    expect($response->getContent())
        ->toEqual('bailed out')
        ->and($response->headers->contains($config['header'], Str::uuid()->toString()))
        ->toBeTrue();
})
->with('config');
