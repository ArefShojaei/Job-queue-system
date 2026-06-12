<?php

namespace App\Jobs;

use Core\Queue\Job;

/**
 * Local Job (Testing) - Sample Job (No Real Logic)
 */
final class SendMailJob implements Job
{
    public function __construct(private string $to) {}

    public function handle(): void
    {
        echo "Sending mail to {$this->to}" . PHP_EOL;

        sleep(2);

        echo "Mail sent." . PHP_EOL;
    }
}
