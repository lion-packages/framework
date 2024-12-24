<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Defines the available roles
 *
 * @package App\Enums
 */
enum RolesEnum: int
{
    /**
     * [Administrator role]
     */
    case ADMINISTRATOR = 1;

    /**
     * [Manager role]
     */
    case MANAGER = 2;

    /**
     * [Customer role]
     */
    case CUSTOMER = 3;

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
