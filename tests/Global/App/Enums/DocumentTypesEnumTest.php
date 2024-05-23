<?php

declare(strict_types=1);

namespace Tests\Global\App\Enums;

use App\Enums\DocumentTypesEnum;
use Lion\Test\Test;

class DocumentTypesEnumTest extends Test
{
    const int CITIZENSHIP_CARD = 1;
    const int PASSPORT = 2;

    public function testValues(): void
    {
        $this->assertSame([self::CITIZENSHIP_CARD, self::PASSPORT], DocumentTypesEnum::values());
    }
}
