<?php

declare(strict_types=1);

namespace Tests\App\Enums;

use App\Enums\RolesEnum;
use Lion\Test\Test;

class RolesEnumTest extends Test
{
    const int ADMINISTRATOR = 1;
    const int MANAGER = 2;
    const int CUSTOMER = 3;

    public function testValues(): void
    {
        $this->assertSame([self::ADMINISTRATOR, self::MANAGER, self::CUSTOMER], RolesEnum::values());
    }
}
