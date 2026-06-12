<?php

namespace Core\Queue;

use Illuminate\Database\Capsule\Manager as DB;
use PhpX\Utils\Console\Console;

use Core\Enums\JobStatus;
use Core\Events\{Event, EventEmitter};
use Core\Exceptions\JobExecutionException;

final class Worker implements Runable
{
    private const PER_ONE_SECOND_TIMER = 1;

    public function __construct(private EventEmitter $emitter) {}

    public function run(): void
    {
        echo Console::log("Starting...", "Queue") . PHP_EOL;

        while (true) {
            try {
                $job = DB::table("jobs")
                    ->where("status", JobStatus::PENDING)
                    ->orderBy("id")
                    ->first();

                if ($job) {
                    $payload = unserialize($job->payload);

                    $name = $payload::class;

                    echo Console::info("Received: \"{$name}\"", "JOB") .
                        PHP_EOL;

                    $this->emitter->emit(Event::JOB_STARTED, $job);

                    is_object($job) &&
                        method_exists($job, "handle") &&
                        $job->handle();

                    DB::table("jobs")
                        ->where("id", $job->id)
                        ->update([
                            "status" => JobStatus::PROCESSING,
                        ]);

                    echo Console::success("Completed: \"{$name}\"", "JOB") .
                        PHP_EOL;

                    $this->emitter->emit(Event::JOB_COMPLETED, $job);

                    DB::table("jobs")
                        ->where("id", $job->id)
                        ->update([
                            "status" => JobStatus::COMPLETED,
                        ]);
                }

                sleep(self::PER_ONE_SECOND_TIMER);
            } catch (JobExecutionException $error) {
                echo Console::error("Failed: \"{$name}\"", "JOB") . PHP_EOL;

                $this->emitter->emit(Event::JOB_FAILED, $error->getMessage());

                DB::table("jobs")
                    ->where("id", $job->id)
                    ->update([
                        "status" => JobStatus::FAILED,
                    ]);
            }
        }
    }
}
