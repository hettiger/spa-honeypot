<?php

use Carbon\CarbonInterval;

return [

    /*
    |--------------------------------------------------------------------------
    | Feature Flag
    |--------------------------------------------------------------------------
    |
    | This feature flag determines whether the anti SPAM protection should
    | be active.
    |
    */

    'enabled' => env('HONEYPOT_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Honeypot Field Name
    |--------------------------------------------------------------------------
    |
    | When the field is filled the request will be blocked.
    | Nested field names using dot notation are supported.
    |
    */

    'field' => 'honey',

    /*
    |--------------------------------------------------------------------------
    | Form Token HTTP Header Name
    |--------------------------------------------------------------------------
    |
    | Used to apply time based anti SPAM protection without cookies.
    | Customize the header name to make things less obvious …
    |
    */

    'header' => 'spa-form-token',

    /*
    |--------------------------------------------------------------------------
    | Cache Key Prefix
    |--------------------------------------------------------------------------
    |
    | Time based anti SPAM protection works by storing a form token in the
    | cache. The cache key is a simple UUID. You may prefix these UUID's
    | to make cache entries recognizable.
    |
    */

    'cache_prefix' => 'spa-form-token-',

    /*
    |--------------------------------------------------------------------------
    | Minimum Form Token Age
    |--------------------------------------------------------------------------
    |
    | The minimum amount of time a human is expected to need for submitting
    | forms.
    |
    */

    'min_age' => CarbonInterval::seconds(3),

    /*
    |--------------------------------------------------------------------------
    | Maximum Form Token Age
    |--------------------------------------------------------------------------
    |
    | The time to live of form token cache entries.
    |
    */

    'max_age' => CarbonInterval::minutes(15),

    /*
    |--------------------------------------------------------------------------
    | Error Response Factories
    |--------------------------------------------------------------------------
    |
    | You may customize the way this package responds to errors by providing
    | custom invokable classes.
    |
    */

    'honeypot_error_response_factory' => \Hettiger\Honeypot\ErrorResponseFactory::class,
    'form_token_error_response_factory' => \Hettiger\Honeypot\ErrorResponseFactory::class,

    /*
    |--------------------------------------------------------------------------
    | Registration of Middleware Aliases and Groups
    |--------------------------------------------------------------------------
    |
    | Per default the package registers middleware aliases and groups. You may
    | disable this behavior if you want to customize the middleware alias or
    | group names. (or if you want to use fully qualified class names …)
    |
    */

    'registers_middleware' => true,

];
