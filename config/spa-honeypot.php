<?php

use Carbon\CarbonInterval;

return [
    'form_token_header' => 'spa-form-token',
    'cache_prefix' => 'spa-form-token-',
    'min_age' => CarbonInterval::seconds(3),
    'max_age' => CarbonInterval::minutes(15),
];
