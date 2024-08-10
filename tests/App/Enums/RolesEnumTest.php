<?php

declare(strict_types=1);

namespace Tests\App\Enums;

use App\Enums\RolesEnum;
use Lion\Test\Test;
use PHPUnit\Framework\Attributes\Test as Testing;

class RolesEnumTest extends Test
{
    private const int ADMINISTRATOR = 1;
    private const int MANAGER = 2;
    private const int CUSTOMER = 3;

    #[Testing]
    public function values(): void
    {
        $this->assertSame([self::ADMINISTRATOR, self::MANAGER, self::CUSTOMER], RolesEnum::values());
    }
}
