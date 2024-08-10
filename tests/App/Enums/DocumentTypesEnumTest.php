<?php

declare(strict_types=1);

namespace Tests\App\Enums;

use App\Enums\DocumentTypesEnum;
use Lion\Test\Test;
use PHPUnit\Framework\Attributes\Test as Testing;

class DocumentTypesEnumTest extends Test
{
    private const int CITIZENSHIP_CARD = 1;
    private const int PASSPORT = 2;

    #[Testing]
    public function values(): void
    {
        $this->assertSame([self::CITIZENSHIP_CARD, self::PASSPORT], DocumentTypesEnum::values());
    }
}
