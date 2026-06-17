<?php

namespace Core\Queue;

use Illuminate\Database\Capsule\Manager as DB;
use PhpX\Utils\Console\Console;

use Core\Enums\JobStatus;
use Core\Events\EventEmitter;
use Core\Exceptions\JobExecutionException;

final class Worker implements Runable
{
    use Processor;

    private const PER_ONE_SECOND_TIMER = 1;

    public function __construct(
        private EventEmitter $emitter,
        private string $table,
    ) {}

    public function run(): void
    {
        echo Console::log("Listening...", "Queue") . PHP_EOL;

        while (true) {
            try {
                $job = null;

                if ($this->table === "jobs") {
                    $job = $this->fetchJob();
                }

                if ($this->table === "failed_jobs") {
                    $job = $this->fetchFailedJob();
                }

                if (!$job) {
                    throw new JobExecutionException("Job not exist");
                }

                if ($job) {
                    $payload = unserialize($job->payload);

                    $name = $payload::class;

                    $this->startJob($name, $payload);

                    $this->processJob($name, $job, $payload);

                    $this->completeJob($name, $job, $payload);
                }

                sleep(self::PER_ONE_SECOND_TIMER);
            } catch (JobExecutionException $error) {
                isset($name) &&
                    isset($job) &&
                    isset($payload) &&
                    $this->failJob($name, $job, $payload, $error);
            }
        }
    }

    private function fetchJob(): ?object
    {
        return DB::table("jobs")
            ->where("status", JobStatus::PENDING)
            ->orderBy("id")
            ->first();
    }

    private function fetchFailedJob(): ?object
    {
        return DB::table("failed_jobs")->orderBy("id")->first();
    }
}
