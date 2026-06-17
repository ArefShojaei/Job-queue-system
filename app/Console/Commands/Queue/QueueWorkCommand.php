<?php

namespace App\Console\Commands\Queue;

use PhpX\Components\Console\Command;

use Core\Events\{Event, EventEmitter};
use Core\Queue\Worker;

final class QueueWorkCommand extends Command
{
    private EventEmitter $emitter;

    public function __construct()
    {
        $this->emitter = new EventEmitter();
    }

    public function exec(array $params): string
    {
        $this->registerJobListeners();

        $table = $params["name"];

        $worker = new Worker($this->emitter, $table);

        $worker->run();

        return "";
    }

    private function registerJobListeners(): void
    {
        $this->emitter->on(Event::JOB_STARTED, function ($job) {});

        $this->emitter->on(Event::JOB_COMPLETED, function ($job) {});

        $this->emitter->on(Event::JOB_FAILED, function ($error) {});
    }
}
