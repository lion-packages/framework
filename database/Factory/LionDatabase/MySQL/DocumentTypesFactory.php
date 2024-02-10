<?php

declare(strict_types=1);

namespace Database\Factory\LionDatabase\MySQL;

class DocumentTypesFactory
{
	/**
	 * Define the model's default state
	 **/
	public static function definition(): array
	{
		return [
            ['Citizenship Card'],
            ['Passport']
        ];
	}
}
