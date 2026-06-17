<?php

namespace App\Console\Commands\Job;

use PhpX\Components\Console\Command;
use Illuminate\Database\Capsule\Manager as DB;

final class FailedJobListCommand extends Command
{
    public function exec(array $params): string
    {
        $jobs = DB::table("failed_jobs")->orderBy("id")->get();

        return print_r($jobs->toArray(), true);
    }
}
