<?php

use Hettiger\Honeypot\HoneypotServiceProvider;
use function Hettiger\Honeypot\resolveByType;
use Illuminate\Routing\Router;
use Mockery\MockInterface;
use function Pest\Laravel\mock;
use function Pest\Laravel\swap;

it('registers middleware', function () {
    $router = resolveByType(Router::class);

    expect(collect($router->getMiddleware())->has([
        'form.honeypot',
        'form.token.handle',
        'form.token.require',
    ]))
        ->toBeTrue()
        ->and($router->hasMiddlewareGroup('form.token'))
        ->toBeTrue()
        ->and($router->hasMiddlewareGroup('form'))
        ->toBeTrue();
});

it('does not register middleware when feature flag is disabled in the config', function () {
    config()->set('registers_middleware', false);

    mock(Router::class, function (MockInterface|Router $router) {
        $router->shouldNotReceive('aliasMiddleware');
        $router->shouldNotReceive('middlewareGroup');
        swap(Router::class, $router);
        $sut = resolveByType(HoneypotServiceProvider::class, ['app' => app()]);

        $sut->registerMiddleware();
    });
});
