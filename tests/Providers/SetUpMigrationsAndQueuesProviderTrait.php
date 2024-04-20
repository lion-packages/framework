<?php

declare(strict_types=1);

namespace Tests\Providers;

use Lion\Command\Kernel;

trait SetUpMigrationsAndQueuesProviderTrait
{
    public function runMigrationsAndQueues(bool $runSeeds = true): void
    {
        $seedOption = $runSeeds ? '--seed' : '';

        (new Kernel())
            ->execute("php lion migrate:fresh {$seedOption} && php lion schedule:schema", false);
    }
}
