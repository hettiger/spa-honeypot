<?php

use Hettiger\Honeypot\FormToken;

dataset('tokens', fn () => [
    'empty token' => fn () => '',
    'invalid token' => fn () => 'invalid-token',
    'valid token' => fn () => FormToken::make()->persisted()->id,
]);
