<?php

use Hettiger\Honeypot\Contracts\TimeSource;
use Hettiger\Honeypot\Contracts\UuidGenerator;
use Hettiger\Honeypot\Tests\TestCase;
use function Pest\Laravel\swap;

uses(TestCase::class)->in(__DIR__);

function withTime(int $time) {
    swap(TimeSource::class, mock(TimeSource::class)->expect(
        now: fn () => $time,
    ));
}

function withUuid(string $id) {
    swap(UuidGenerator::class, mock(UuidGenerator::class)->expect(
        uuid: fn () => $id,
    ));
}