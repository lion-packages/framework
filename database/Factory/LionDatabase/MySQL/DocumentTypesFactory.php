<?php

declare(strict_types=1);

namespace Database\Factory\LionDatabase\MySQL;

use Lion\Bundle\Interface\FactoryInterface;

class DocumentTypesFactory implements FactoryInterface
{
	/**
	 * {@inheritdoc}
	 **/
	public static function definition(): array
	{
		return [
            ['Citizenship Card'],
            ['Passport']
        ];
	}
}
