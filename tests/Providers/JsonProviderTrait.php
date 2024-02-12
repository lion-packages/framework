<?php

declare(strict_types=1);

namespace Tests\Providers;

use Lion\Test\Test;

trait JsonProviderTrait
{
    public function assertFetchJson(Test $test, string $json, array $options): void
    {
        $test->assertSame($options, json_decode($json, true));
    }
}
