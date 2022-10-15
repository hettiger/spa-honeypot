<?php

namespace Hettiger\Honeypot;

use Hettiger\Honeypot\Commands\HoneypotCommand;
use Hettiger\Honeypot\Contracts\Cache;
use Hettiger\Honeypot\Contracts\TimeSource;
use Hettiger\Honeypot\Contracts\UuidGenerator;
use Illuminate\Support\Facades\Cache as CacheFacade;
use Illuminate\Support\Str;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class HoneypotServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('spa-honeypot')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_spa-honeypot_table')
            ->hasCommand(HoneypotCommand::class);
    }

    public function register()
    {
        parent::register();

        $this->app->singletonIf(
            TimeSource::class,
            fn () => new class implements TimeSource
            {
                public function now(): int
                {
                    return now()->timestamp;
                }
            }
        );

        $this->app->singletonIf(
            UuidGenerator::class,
            fn () => new class implements UuidGenerator
            {
                public function uuid(): string
                {
                    return Str::uuid()->toString();
                }
            }
        );

        $this->app->singletonIf(
            Cache::class,
            fn () => new class implements Cache
            {
                public function put($key, $value, $ttl = null)
                {
                    return CacheFacade::put($key, $value, $ttl);
                }

                public function pull($key, $default = null)
                {
                    return CacheFacade::pull($key, $default);
                }
            }
        );
    }

    public function boot()
    {
        parent::boot();

        $this->app->when(FormToken::class)
            ->needs('$config')
            ->give(config('spa-honeypot'));
    }
}
