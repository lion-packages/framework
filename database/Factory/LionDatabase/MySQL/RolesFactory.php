<?php

declare(strict_types=1);

namespace Database\Factory\LionDatabase\MySQL;

class RolesFactory
{
	/**
	 * Define the model's default state
	 **/
	public static function definition(): array
	{
		return [
            ['Administrator', 'Administrator description'],
            ['Manager', 'Manager description'],
            ['Customer', 'Customer description']
        ];
	}
}
