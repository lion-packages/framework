<?php

declare(strict_types=1);

namespace Tests;

use Lion\Bundle\Test\Test;
use PHPUnit\Framework\Attributes\Test as Testing;

class AppTest extends Test
{
    protected function setUp(): void
    {
    }

    protected function tearDown(): void
    {
    }

    #[Testing]
    public function example(): void
    {
        /** @phpstan-ignore-next-line */
        $this->assertTrue(true);
    }
}
