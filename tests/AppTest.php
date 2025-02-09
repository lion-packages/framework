<?php

declare(strict_types=1);

namespace Tests;

use Lion\Bundle\Test\Test;
use PHPUnit\Framework\Attributes\Test as Testing;

class AppTest extends Test
{
    #[Testing]
    public function app(): void
    {
        $this->assertTrue(true);
    }
}
