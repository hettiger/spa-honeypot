# Honeypot package for Single Page Applications

[![Latest Version on Packagist](https://img.shields.io/packagist/v/hettiger/spa-honeypot.svg?style=flat-square)](https://packagist.org/packages/hettiger/spa-honeypot)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/hettiger/spa-honeypot/run-tests?label=tests)](https://github.com/hettiger/spa-honeypot/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/hettiger/spa-honeypot/Fix%20PHP%20code%20style%20issues?label=code%20style)](https://github.com/hettiger/spa-honeypot/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/hettiger/spa-honeypot.svg?style=flat-square)](https://packagist.org/packages/hettiger/spa-honeypot)

Helps to protect SPA's (Single Page Applications) against SPAM without using cookies or user input.

## Installation

You can install the package via composer:

```bash
composer require hettiger/spa-honeypot
php artisan spa-honeypot:install
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="spa-honeypot-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="spa-honeypot-config"
```

This is the contents of the published config file:

```php
use Carbon\CarbonInterval;

return [
    'field' => 'honey',
    'header' => 'spa-form-token',
    'cache_prefix' => 'spa-form-token-',
    'min_age' => CarbonInterval::seconds(3),
    'max_age' => CarbonInterval::minutes(15),
    'honeypot_error_response_factory' => \Hettiger\Honeypot\ErrorResponseFactory::class,
    'form_token_error_response_factory' => \Hettiger\Honeypot\ErrorResponseFactory::class,
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="spa-honeypot-views"
```

## Usage

### REST API

1. Add the `form.honeypot`, `form.token`  or `form` middleware to a forms target route

```php
Route::post('form', fn () => 'OK')->middleware('form');
```

> The `form` middleware group simply combines `form.honeypot` and `form.token` so you don't have to.
> Using just `form.token` protection without the `form.honeypot` middleware or vise versa is supported. 

2. Use one of the corresponding frontend libraries to make form token requests 

### Lighthouse GraphQL API

1. Add the `form.token.handle` middleware to the `lighthouse.route.middleware` config

```php
// config/lighthouse.php — must be published

'middleware' => [
    // …

    'form.token.handle',
],
```

2. Add the `@requireFormToken` directive to any field that you want to protect against SPAM

```graphql
# e.g. graphql/contact.graphql

extend type Mutation {
    sendContactRequest(input: SendContactRequestInput): SendContactRequestPayload @requireFormToken
}
```

3. Use one of the corresponding frontend libraries to make form token requests

### Customizing Responses

You may provide custom error response factories using the config:

```php
return [
    // …
    
    // provide a invokable class to be used as the honeypot error response factory here
    'honeypot_error_response_factory' => \Hettiger\Honeypot\ErrorResponseFactory::class,
    
    // provide a invokable class to be used as the form token error response factory here
    'form_token_error_response_factory' => \Hettiger\Honeypot\ErrorResponseFactory::class,
];
```

> See `\Hettiger\Honeypot\ErrorResponseFactory::class` to learn how to implement such a factory.

Alternatively you can provide a simple `Closure` anywhere in your application:

```php
use Hettiger\Honeypot\Facades\Honeypot;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    // …

    public function boot()
    {
        $errorResponseFactory = fn (bool $isGraphQLRequest) => $isGraphQLRequest
            ? ['errors' => [['message' => 'Whoops, something went wrong …']]]
            : 'Whoops, something went wrong …';

        Honeypot::respondToHoneypotErrorsUsing($errorResponseFactory);
        Honeypot::respondToFormTokenErrorsUsing($errorResponseFactory);
    }
}
```

> You don't have to worry about adding the form token header yourself. It'll be added for you automatically.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Martin Hettiger](https://github.com/hettiger)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
