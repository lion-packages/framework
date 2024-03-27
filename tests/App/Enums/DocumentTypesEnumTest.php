<?php

declare(strict_types=1);

namespace Tests\App\Enums;

use App\Enums\DocumentTypesEnum;
use Lion\Test\Test;

class DocumentTypesEnumTest extends Test
{
    const CITIZENSHIP_CARD = 1;
    const PASSPORT = 2;

    public function testValues(): void
    {
        $this->assertSame([self::CITIZENSHIP_CARD, self::PASSPORT], DocumentTypesEnum::values());
    }
}
