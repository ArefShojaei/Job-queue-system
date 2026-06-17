<?php

namespace App\Console\Commands\Job;

use PhpX\Components\Console\Command;
use PhpX\Utils\Console\Console;
use Illuminate\Database\Capsule\Manager as DB;
use Core\Enums\JobStatus;
use App\Jobs\SendMailJob;
use App\Jobs\SendWelcomeMessageJob;

final class JobTestCommand extends Command
{
    public function exec(array $params): string
    {
        $job = DB::table("jobs")->insert([
            [
                "payload" => serialize(new SendMailJob("aref@gmail.com")),
                "status" => JobStatus::PENDING,
            ],
            [
                "payload" => serialize(new SendWelcomeMessageJob()),
                "status" => JobStatus::PENDING,
            ],
        ]);
        // $job = DB::table("jobs")->insert([
        //     "payload" => serialize(new SendMailJob("aref@gmail.com")),
        //     "status" => JobStatus::PENDING,
        // ]);

        return $job
            ? Console::success("Added.", "JOB")
            : Console::error("Failed.", "JOB");
    }
}
