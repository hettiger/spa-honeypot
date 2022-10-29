<?php

namespace Hettiger\Honeypot;

use Hettiger\Honeypot\Commands\HoneypotCommand;
use Hettiger\Honeypot\Http\Middleware\HandleFormTokenRequests;
use Hettiger\Honeypot\Http\Middleware\RequireFormToken;
use Illuminate\Events\Dispatcher;
use Illuminate\Routing\Router;
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

        $this->registerMiddleware();
        $this->registerGraphQLNamespaces();
    }

    protected function registerMiddleware(): void
    {
        $router = resolveByType(Router::class);

        $router->aliasMiddleware('form.token.handle', HandleFormTokenRequests::class);
        $router->aliasMiddleware('form.token.require', RequireFormToken::class);
        $router->middlewareGroup('form.token', ['form.token.handle', 'form.token.require']);
    }

    protected function registerGraphQLNamespaces()
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
