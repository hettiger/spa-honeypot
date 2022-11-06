<?php

namespace Hettiger\Honeypot;

use Hettiger\Honeypot\Http\Middleware\AbortWhenHoneypotIsFilled;
use Hettiger\Honeypot\Http\Middleware\HandleFormTokenRequests;
use Hettiger\Honeypot\Http\Middleware\RequireFormToken;
use Illuminate\Events\Dispatcher;
use Illuminate\Routing\Router;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
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
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->askToStarRepoOnGitHub('hettiger/spa-honeypot');
            });
    }

    public function boot()
    {
        parent::boot();

        $this->registerMiddleware();
        $this->registerGraphQLNamespaces();
    }

    public function registerMiddleware(): void
    {
        if (! config('registers_middleware')) {
            return;
        }

        $router = resolveByType(Router::class);

        $router->aliasMiddleware('form.honeypot', AbortWhenHoneypotIsFilled::class);
        $router->aliasMiddleware('form.token.handle', HandleFormTokenRequests::class);
        $router->aliasMiddleware('form.token.require', RequireFormToken::class);
        $router->middlewareGroup('form.token', ['form.token.handle', 'form.token.require']);
        $router->middlewareGroup('form', ['form.honeypot', 'form.token']);
    }

    public function registerGraphQLNamespaces()
    {
        $events = resolveByType(Dispatcher::class);

        $events->listen(
            'Nuwave\\Lighthouse\\Events\\RegisterDirectiveNamespaces',
            fn () => [
                'Hettiger\\Honeypot\\GraphQL\Directives',
            ]
        );
    }
}
