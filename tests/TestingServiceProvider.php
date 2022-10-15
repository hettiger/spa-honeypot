<?php

namespace Hettiger\Honeypot\Tests;

use Hettiger\Honeypot\Contracts\Cache;
use Hettiger\Honeypot\Contracts\TimeSource;
use Hettiger\Honeypot\Contracts\UuidGenerator;
use Hettiger\Honeypot\FormToken;
use Hettiger\Honeypot\Tests\Fakes\CacheFake;
use Illuminate\Support\ServiceProvider;
use Pest\Mock\Mock;

class TestingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $defaultExpectations = fn (Mock $mock) => $mock->expect();

        $timeSource = ($timeSourceExpectations ?? $defaultExpectations)(mock(TimeSource::class));
        $this->app->instance(TimeSource::class, $timeSource);

        $uuidGenerator = ($uuidGeneratorExpectations ?? $defaultExpectations)(mock(UuidGenerator::class));
        $this->app->instance(UuidGenerator::class, $uuidGenerator);

        $this->app->singleton(Cache::class, CacheFake::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->when(FormToken::class)
            ->needs('$config')
            ->give(config('spa-honeypot'));
    }
}
