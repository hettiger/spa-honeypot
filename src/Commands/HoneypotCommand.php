<?php

namespace Hettiger\Honeypot\Commands;

use Illuminate\Console\Command;

class HoneypotCommand extends Command
{
    public $signature = 'spa-honeypot';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
