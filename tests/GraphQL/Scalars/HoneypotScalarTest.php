<?php

use GraphQL\Error\Error;
use GraphQL\Language\AST\NullValueNode;
use Hettiger\Honeypot\Facades\Honeypot;
use Hettiger\Honeypot\GraphQL\Scalars\HoneypotScalar;
use Hettiger\Honeypot\Tests\Fakes\NodeFake;

it('does not support serialization', function () {
    $sut = new HoneypotScalar();
    $value = 'value-fake';

    expect(fn () => $sut->serialize($value))->toThrow(
        fn (Error $e) => expect($e->getMessage())
            ->toBe('Serializing honeypot is not supported.')
    );
});

it('bails out on empty $value', function ($value) {
    $sut = new HoneypotScalar();

    expect($sut->parseValue($value))
        ->toBe($value)
        ->and($sut->parseLiteral(new NodeFake($value)))
        ->toBe($value);
})
->with([
    null,
    '',
]);

it('aborts on non-empty $value', function () {
    $sut = new HoneypotScalar();
    $value = 'value-fake';

    expect(fn () => $sut->parseValue($value))
        ->toAbortWith(500);

    expect(fn () => $sut->parseLiteral(new NodeFake($value)))
        ->toAbortWith(500);
});

it('aborts using custom response factory when available', function () {
    $sut = new HoneypotScalar();
    $value = 'value-fake';
    $expectedResponse = response(['data' => 'response fake']);
    Honeypot::respondToHoneypotErrorsUsing(fn () => $expectedResponse);

    expect(fn () => $sut->parseValue($value))
        ->toAbortWithResponse($expectedResponse);

    expect(fn () => $sut->parseLiteral(new NodeFake($value)))
        ->toAbortWithResponse($expectedResponse);
});

it('does not support $valueNode without value', function () {
    $sut = new HoneypotScalar();

    expect(fn () => $sut->parseLiteral(new NullValueNode([])))
        ->toThrow(fn (Error $e) => expect($e->getMessage())
            ->toBe('Type of $valueNode does not provide a value property.')
        );
});
