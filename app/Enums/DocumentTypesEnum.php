<?php

declare(strict_types=1);

namespace App\Enums;

enum DocumentTypesEnum: int
{
	case CITIZENSHIP_CARD = 1;
    case PASSPORT = 2;

	public static function values(): array
	{
		return array_map(fn($value) => $value->value, self::cases());
	}
}
