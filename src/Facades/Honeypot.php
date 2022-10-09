<?php

namespace Hettiger\Honeypot\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Hettiger\Honeypot\Honeypot
 */
class Honeypot extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Hettiger\Honeypot\Honeypot::class;
    }
}
