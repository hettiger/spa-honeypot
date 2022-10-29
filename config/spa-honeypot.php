<?php

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
