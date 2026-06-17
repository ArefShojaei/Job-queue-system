<?php

namespace App\Console\Commands\Database;

use Exception;
use PhpX\Components\Console\Command;
use PhpX\Utils\Console\Console;
use Illuminate\Database\Capsule\Manager as DB;

final class DatabaseInitializeTableCommand extends Command
{
    public function exec(array $params): string
    {
        try {
            $this->createJobTable();

            $this->createFailedJobTable();

            return Console::success("Job tables were created", "DATABASE") .
                PHP_EOL;
        } catch (Exception $e) {
            return Console::error($e->getMessage(), "DATABASE") . PHP_EOL;
        }
    }

    private function createJobTable(): void
    {
        DB::statement("CREATE TABLE IF NOT EXISTS `jobs` (
            id INT AUTO_INCREMENT PRIMARY KEY,
            payload LONGTEXT NOT NULL,
            status ENUM(
                'pending',
                'processing',
                'completed',
                'failed'
            ) DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                ON UPDATE CURRENT_TIMESTAMP
        );");
    }

    private function createFailedJobTable(): void
    {
        DB::statement("CREATE TABLE IF NOT EXISTS `failed_jobs` (
            id INT AUTO_INCREMENT PRIMARY KEY,
            payload LONGTEXT NOT NULL,
            status ENUM(
                'pending',
                'processing',
                'completed',
                'failed'
            ) DEFAULT 'pending',
            failed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );");
    }
}
