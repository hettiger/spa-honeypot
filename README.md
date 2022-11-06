# Honeypot package for Single Page Applications

[![Latest Version on Packagist](https://img.shields.io/packagist/v/hettiger/spa-honeypot.svg?style=flat-square)](https://packagist.org/packages/hettiger/spa-honeypot)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/hettiger/spa-honeypot/run-tests?label=tests)](https://github.com/hettiger/spa-honeypot/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/hettiger/spa-honeypot/Fix%20PHP%20code%20style%20issues?label=code%20style)](https://github.com/hettiger/spa-honeypot/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/hettiger/spa-honeypot.svg?style=flat-square)](https://packagist.org/packages/hettiger/spa-honeypot)

Helps to protect SPA's (Single Page Applications) against SPAM without using cookies or user input.

## Installation

```bash
composer require hettiger/spa-honeypot
php artisan spa-honeypot:install
```

## Usage

1. Add the `form.honeypot`, `form.token`  or `form` middleware to a forms target route

```php
Route::post('form', fn () => 'OK')->middleware('form');
```

> The `form` middleware group simply combines `form.honeypot` and `form.token` so you don't have to.
> Using just `form.token` protection without the `form.honeypot` middleware or vise versa is supported. 

2. Use one of the corresponding [frontend libraries](#frontend-libraries) to make form token requests 

### Lighthouse GraphQL API

1. Add the `form.token.handle` middleware to the `lighthouse.route.middleware` config

```php
// config/lighthouse.php — must be published

'middleware' => [
    // …

    'form.token.handle',
],
```

2. Register the honeypot scalar in your `graphql/schema.graphql` file

```graphql
scalar Honeypot @scalar(class: "Hettiger\\Honeypot\\GraphQL\\Scalars\\HoneypotScalar")

# …
```

3. Add a honeypot field to any input that you want to protect against SPAM

```graphql
input SendContactRequestInput {
    # …
    honey: Honeypot
}
```

> The `field` config is not being used in GraphQL context.

4. Add the `@requireFormToken` directive to any field that you want to protect against SPAM

```graphql
# e.g. graphql/contact.graphql

extend type Mutation {
    sendContactRequest(input: SendContactRequestInput): SendContactRequestPayload @requireFormToken
}
```

5. Use one of the corresponding [frontend libraries](#frontend-libraries) to make form token requests

### Customizing Responses

You may provide custom error response factories using the config:

```php
return [
    // …
    
    'honeypot_error_response_factory' => \Hettiger\Honeypot\ErrorResponseFactory::class,
    'form_token_error_response_factory' => \Hettiger\Honeypot\ErrorResponseFactory::class,
];
```

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

## Frontend Libraries

Nothing released yet, this is still a work in progress …

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Martin Hettiger](https://github.com/hettiger)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
