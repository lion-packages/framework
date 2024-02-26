<?php

declare(strict_types=1);

namespace Database\Factory\LionDatabase\MySQL;

use Lion\Bundle\Interface\FactoryInterface;

class RolesFactory implements FactoryInterface
{
	/**
	 * {@inheritdoc}
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
