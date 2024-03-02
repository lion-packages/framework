<?php

declare(strict_types=1);

namespace Database\Seed\LionDatabase\MySQL;

use Database\Factory\LionDatabase\MySQL\RolesFactory;
use Lion\Bundle\Interface\SeedInterface;
use Lion\Database\Drivers\MySQL as DB;

class RolesSeed implements SeedInterface
{
    const INDEX = 2;
    const COLUMNS = ['roles_name', 'roles_description'];

	/**
	 * {@inheritdoc}
	 **/
	public function run(): object
	{
		return DB::table('roles')
            ->bulk(self::COLUMNS, RolesFactory::definition())
            ->execute();
	}
}
