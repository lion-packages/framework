<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Defines the types of documents available
 *
 * @package App\Enums
 */
enum DocumentTypesEnum: int
{
    /**
     * [Identification document]
     */
    case CITIZENSHIP_CARD = 1;

    /**
     * [Passport]
     */
    case PASSPORT = 2;

    /**
     * Return a list with the different types of responses available
     *
     * @return array<int>
     */
    public static function values(): array
    {
        return array_map(fn($value) => $value->value, self::cases());
    }
}
