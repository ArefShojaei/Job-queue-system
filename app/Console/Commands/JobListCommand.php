<?php

namespace App\Console\Commands;

use PhpX\Components\Console\Command;
use Illuminate\Database\Capsule\Manager as DB;

final class JobListCommand extends Command
{
    public function exec(array $params): string
    {
        $jobs = DB::table("jobs")->orderBy("id")->get();

        if (!count($jobs)) {
            $jobs = [];
        }

        return var_export($jobs, true);
    }
}
