<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use PhpX\Components\Console\App as Console;

use App\Console\Commands\{
    JobCleanCommand,
    JobListCommand,
    JobTestCommand,
    QueueWorkCommand,
};

final class Application
{
    private Console $console;

    public function boot(): void
    {
        $this->loadEnvironment();
        $this->bootDatabase();
    }

    public function run(): void
    {
        $this->registerCommands();

        $this->console->launch();
    }

    private function loadEnvironment(): void
    {
        $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));

        $dotenv->safeLoad();
    }

    private function bootDatabase(): void
    {
        $capsule = new Capsule();

        $capsule->addConnection([
            "driver" => $_ENV["DB_DRIVER"],
            "host" => $_ENV["DB_HOST"],
            "database" => $_ENV["DB_REPOSITORY"],
            "username" => $_ENV["DB_USERNAME"],
            "password" => $_ENV["DB_PASSWORD"],
            "charset" => "utf8mb4",
            "collation" => "utf8mb4_unicode_ci",
        ]);

        $capsule->setAsGlobal();

        $capsule->bootEloquent();
    }

    private function registerCommands(): void
    {
        $console = new Console();

        $console->group("job:", function ($console) {
            $console->command("clean", new JobCleanCommand());
            $console->command("test", new JobTestCommand());
            $console->command("list", new JobListCommand());
        });

        $console->command("queue:work", new QueueWorkCommand());

        $this->console = $console;
    }
}
