<?php

use Hettiger\Honeypot\Tests\TestCase;
use Illuminate\Support\Carbon;
use function Pest\Laravel\travelTo;

uses(TestCase::class)->in(__DIR__);

function freezeTimestamp(int $timestamp): void
{
    travelTo(Carbon::createFromTimestamp($timestamp));
}
