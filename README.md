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
return [
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="spa-honeypot-views"
```

## Usage

### REST API

1. Add the `form.token` middleware group to a forms target route

```php
Route::post('form', fn () => 'OK')->middleware('form.token');
```

2. Use one of the corresponding frontend libraries to make form token requests 

### Lighthouse GraphQL API

1. Add the `form.token.handle` middleware to the `lighthouse.route.middleware` config

```php
// config/lighthouse.php — must be published

/*
 * Beware that middleware defined here runs before the GraphQL execution phase,
 * make sure to return spec-compliant responses in case an error is thrown.
 */
'middleware' => [
    \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,

    \Nuwave\Lighthouse\Support\Http\Middleware\AcceptJson::class,

    // Logs in a user if they are authenticated. In contrast to Laravel's 'auth'
    // middleware, this delegates auth and permission checks to the field level.
    \Nuwave\Lighthouse\Support\Http\Middleware\AttemptAuthentication::class,

    // Logs every incoming GraphQL query.
    // \Nuwave\Lighthouse\Support\Http\Middleware\LogGraphQLQueries::class,

    'form.token.handle',
],
```

2. Add the `@requireFormToken` directive to any input that you want to guard against SPAM

```graphql
# e.g. graphql/contact.graphql

input ContactRequestInput @requireFormToken {
    name: String! @rules(apply: ["string", "max:120"])
    email: String! @rules(apply: ["string", "email", "max:254"])
    subject: String! @rules(apply: ["string", "max:78"])
    message: String! @rules(apply: ["string", "max:10000"])
    consent: Boolean! @rules(apply: ["accepted"])
}
```

3. Use one of the corresponding frontend libraries to make form token requests

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
