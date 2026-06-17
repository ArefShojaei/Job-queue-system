<?php

namespace App\Console\Commands\Database;

use Exception;
use PhpX\Components\Console\Command;
use PhpX\Utils\Console\Console;
use Illuminate\Database\Capsule\Manager as DB;

final class DatabaseDropTableCommand extends Command
{
    public function exec(array $params): string
    {
        try {
            $this->dropJobTable();

            $this->dropFailedJobTable();

            return Console::success("Job tables were deleted", "DATABASE") .
                PHP_EOL;
        } catch (Exception $e) {
            return Console::error($e->getMessage(), "DATABASE") . PHP_EOL;
        }
    }

    private function dropJobTable(): void
    {
        DB::statement("DROP TABLE IF EXISTS `jobs`;");
    }

    private function dropFailedJobTable(): void
    {
        DB::statement("DROP TABLE IF EXISTS `failed_jobs`;");
    }
}
