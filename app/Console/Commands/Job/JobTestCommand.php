<?php

namespace App\Console\Commands;

use PhpX\Components\Console\Command;
use PhpX\Utils\Console\Console;
use Illuminate\Database\Capsule\Manager as DB;
use Core\Enums\JobStatus;
use App\Jobs\SendMailJob;

final class JobTestCommand extends Command
{
    public function exec(array $params): string
    {
        $job = DB::table("jobs")->insert([
            "payload" => serialize(new SendMailJob("aref@gmail.com")),
            "status" => JobStatus::PENDING,
        ]);

        return $job
            ? Console::success("Added.", "JOB")
            : Console::error("Failed.", "JOB");
    }
}
