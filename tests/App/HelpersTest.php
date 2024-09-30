<?php

declare(strict_types=1);

namespace Tests\App;

use Lion\Test\Test;
use PHPUnit\Framework\Attributes\Test as Testing;

class HelpersTest extends Test
{
    #[Testing]
    public function helloWorld(): void
    {
        $helloWorld = helloWorld();

        $this->assertIsString($helloWorld);
        $this->assertStringContainsString('Hello World', $helloWorld);
    }
}
