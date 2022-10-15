<?php

namespace Hettiger\Honeypot\Contracts;

interface TimeSource
{
    /**
     * Returns current timestamp in seconds
     */
    public function now(): int;
}
