<?php

use Carbon\CarbonInterval;

return [
    'min_age' => CarbonInterval::seconds(3),
    'max_age' => CarbonInterval::minutes(15),
];
