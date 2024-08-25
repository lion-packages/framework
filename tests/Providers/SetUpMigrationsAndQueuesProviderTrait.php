<?php

declare(strict_types=1);

namespace Tests\Providers;

use Lion\Bundle\Helpers\Commands\ProcessCommand;

trait SetUpMigrationsAndQueuesProviderTrait
{
    public function runMigrations(bool $runSeeds = true): void
    {
        $seedOption = $runSeeds ? '--seed' : '';

        ProcessCommand::run("php lion migrate:fresh {$seedOption}", false);
    }
}
