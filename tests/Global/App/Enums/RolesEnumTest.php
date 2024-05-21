<?php

declare(strict_types=1);

namespace Tests\Global\App\Enums;

use App\Enums\RolesEnum;
use Lion\Test\Test;

class RolesEnumTest extends Test
{
    const ADMINISTRATOR = 1;
    const MANAGER = 2;
    const CUSTOMER = 3;

    public function testValues(): void
    {
        $this->assertSame([self::ADMINISTRATOR, self::MANAGER, self::CUSTOMER], RolesEnum::values());
    }
}
