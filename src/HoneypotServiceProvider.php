<?php

namespace Hettiger\Honeypot;

use Hettiger\Honeypot\Commands\HoneypotCommand;
use Hettiger\Honeypot\Http\Middleware\RespondWithFormToken;
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

    public function boot()
    {
        parent::boot();

        $this->app->when([
            FormToken::class,
            RespondWithFormToken::class,
        ])
            ->needs('$config')
            ->give(config('spa-honeypot'));
    }
}
