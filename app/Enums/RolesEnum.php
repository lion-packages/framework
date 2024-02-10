<?php

declare(strict_types=1);

namespace App\Enums;

enum RolesEnum: int
{
	case ADMINISTRATOR = 1;
    case MANAGER = 2;
    case CUSTOMER = 3;

	public static function values(): array
	{
		return array_map(fn($value) => $value->value, self::cases());
	}
}
