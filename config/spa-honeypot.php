<?php

use Carbon\CarbonInterval;

return [
    'cache_prefix' => 'spa-honeypot-',
    'min_age' => CarbonInterval::seconds(3),
    'max_age' => CarbonInterval::minutes(15),
];
