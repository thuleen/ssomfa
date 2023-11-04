<?php

namespace Thuleen\Ssomfa\Commands;

use Illuminate\Console\Command;

class SsomfaCommand extends Command
{
    public $signature = 'ssomfa';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
