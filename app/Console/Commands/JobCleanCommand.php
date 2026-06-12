<?php

namespace App\Console\Commands;

use PhpX\Components\Console\Command;
use PhpX\Utils\Console\Console;
use Illuminate\Database\Capsule\Manager as DB;

final class JobCleanCommand extends Command
{
    public function exec(array $params): string
    {
        $job = DB::table("jobs")->delete();

        return $job
            ? Console::success("Cleared", "JOB")
            : Console::error("Failed.", "JOB");
    }
}
