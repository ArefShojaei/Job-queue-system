<?php

namespace App\Console\Commands\Database;

use Exception;
use PhpX\Components\Console\Command;
use PhpX\Utils\Console\Console;
use Illuminate\Database\Capsule\Manager as DB;

final class DatabaseCleanTableCommand extends Command
{
    public function exec(array $params): string
    {
        try {
            $this->cleanJobTable();

            $this->cleanFailedJobTable();

            return Console::success("Job tables were cleaned", "DATABASE") .
                PHP_EOL;
        } catch (Exception $e) {
            return Console::error($e->getMessage(), "DATABASE") . PHP_EOL;
        }
    }

    private function cleanJobTable(): void
    {
        DB::statement("TRUNCATE TABLE `jobs`;");
    }

    private function cleanFailedJobTable(): void
    {
        DB::statement("TRUNCATE TABLE `failed_jobs`;");
    }
}
