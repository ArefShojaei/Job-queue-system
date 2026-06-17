<?php

namespace Core\Queue;

use Throwable;
use Illuminate\Database\Capsule\Manager as DB;
use PhpX\Utils\Console\Console;

use Core\Enums\JobStatus;
use Core\Events\Event;

trait Processor
{
    private function startJob(string $name, object $payload): void
    {
        echo Console::info("Pendding: \"{$name}\"", "JOB") . PHP_EOL;

        $this->emitter->emit(Event::JOB_STARTED, $payload);
    }

    private function processJob(
        string $name,
        object &$job,
        object $payload,
    ): void {
        DB::table($this->table)
            ->where("id", $job->id)
            ->update([
                "status" => JobStatus::PROCESSING,
            ]);

        $job = DB::table($this->table)->find($job->id);

        echo Console::info("Processing: \"{$name}\"", "JOB") . PHP_EOL;

        try {
            is_object($payload) &&
                is_subclass_of($payload, Job::class) &&
                method_exists($payload, "handle") &&
                $payload->handle();
        } catch (Throwable $error) {
            $this->failJob($name, $job, $payload, $error);
        }
    }

    private function completeJob(
        string $name,
        object &$job,
        object $payload,
    ): void {
        if ($job->status === "failed") {
            return;
        }

        DB::table($this->table)
            ->where("id", $job->id)
            ->update([
                "status" => JobStatus::COMPLETED,
            ]);

        $job = DB::table($this->table)->find($job->id);

        echo Console::success("Completed: \"{$name}\"", "JOB") . PHP_EOL;

        $this->emitter->emit(Event::JOB_COMPLETED, $payload);

        DB::table($this->table)->delete($job->id);
    }

    private function failJob(
        string $name,
        object &$job,
        object $payload,
        object $error,
    ): void {
        DB::table($this->table)
            ->where("id", $job->id)
            ->update([
                "status" => JobStatus::FAILED,
            ]);

        $job = DB::table($this->table)->find($job->id);

        echo Console::error("Failed: \"{$name}\"", "JOB") . PHP_EOL;

        $payload->error = $error;

        $this->emitter->emit(Event::JOB_FAILED, $payload);

        DB::table($this->table)
            ->where("id", $job->id)
            ->orWhere("status", JobStatus::FAILED)
            ->delete();

        if ($this->table !== "failed_jobs") {
            DB::table("failed_jobs")->insert([
                "payload" => $job->payload,
            ]);
        }
    }
}
