<?php

namespace Hettiger\Honeypot\Tests;

use Hettiger\Honeypot\FormToken;
use Illuminate\Support\ServiceProvider;

class TestingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
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
