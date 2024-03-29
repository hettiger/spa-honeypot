<?php

use function Hettiger\Honeypot\config;
use Hettiger\Honeypot\Facades\Honeypot;
use Hettiger\Honeypot\FormToken;
use Hettiger\Honeypot\Http\Middleware\HandleFormTokenRequests;
use function Hettiger\Honeypot\resolveByType;
use Hettiger\Honeypot\Tests\Fakes\ResponsableFake;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

beforeEach(function () {
    Str::freezeUuids();
});

afterEach(function () {
    Str::createUuidsNormally();
});

it('bails out when package is not enabled', function () {
    config()->set('spa-honeypot.enabled', false);
    $sut = resolveByType(HandleFormTokenRequests::class);
    $request = withRequest();
    $request->headers->set(config('header'), '');
    $expectedResponse = response('bailed out');

    $actualResponse = $sut->handle($request, fn () => $expectedResponse);

    expect($actualResponse)
        ->toBe($expectedResponse)
        ->and($actualResponse->headers->has(config('header')))
        ->toBeFalse();
});

it('bails out when header is missing', function () {
    $sut = resolveByType(HandleFormTokenRequests::class);
    $request = withRequest();
    $expectedResponse = response('bailed out');

    $actualResponse = $sut->handle($request, fn () => $expectedResponse);

    expect($actualResponse)
        ->toBe($expectedResponse)
        ->and($actualResponse->headers->has(config('header')))
        ->toBeFalse();
});

it('aborts when an invalid or empty token is present in the header', function (string $token) {
    $sut = resolveByType(HandleFormTokenRequests::class);
    $request = withRequest();
    $request->headers->set(config('header'), $token);

    expect(fn () => $sut->handle($request, fn () => 'bailed out'))
        ->toAbortWith(
            Response::HTTP_INTERNAL_SERVER_ERROR,
            headers: [config('header') => Str::uuid()->toString()]
        );
})
->with('tokens');

it('aborts with GraphQL spec conforming errors on GraphQL requests', function (string $token) {
    $sut = resolveByType(HandleFormTokenRequests::class);
    $request = withGraphQLRequest();
    $request->headers->set(config('header'), $token);

    expect(fn () => $sut->handle($request, fn () => 'bailed out'))
        ->toThrow(fn (HttpResponseException $exception) => expect($exception->getResponse()->getContent())
            ->toEqual(json_encode(['errors' => [['message' => 'Internal Server Error']]]))
            ->and($exception->getResponse()->headers->contains(config('header'), Str::uuid()->toString()))
        );
})
->with('tokens');

it('aborts with custom error response when present', function (string $token) {
    $sut = resolveByType(HandleFormTokenRequests::class);
    $request = withRequest();
    $request->headers->set(config('header'), $token);
    $expectedResponse = response('response fake');

    Honeypot::respondToFormTokenErrorsUsing(fn () => $expectedResponse);

    expect(fn () => $sut->handle($request, fn () => 'bailed out'))
        ->toAbortWithResponse(
            $expectedResponse,
            fn (ResponseHeaderBag $bag) => $bag->contains(config('header'), Str::uuid()->toString())
        );
})
->with('tokens');

it('adds a new token to the response when a valid token is present in the header', function () {
    $sut = resolveByType(HandleFormTokenRequests::class);
    $request = withRequest();
    $request->headers->set(config('header'), FormToken::make()->persisted()->id);
    simulateSlowHuman();

    $response = $sut->handle($request, fn () => 'bailed out');

    expect($response->getContent())
        ->toEqual('bailed out')
        ->and($response->headers->contains(config('header'), Str::uuid()->toString()))
        ->toBeTrue();
});

it("can add new token header even if it has to deal with responsable's", function () {
    $sut = resolveByType(HandleFormTokenRequests::class);
    $request = withRequest();
    $request->headers->set(config('header'), FormToken::make()->persisted()->id);
    $expectedResponse = response('response fake');
    simulateSlowHuman();

    $actualResponse = $sut->handle($request, fn () => new ResponsableFake($expectedResponse));

    expect($actualResponse)
        ->toBe($expectedResponse)
        ->and($actualResponse->headers->contains(config('header'), Str::uuid()->toString()));
});
