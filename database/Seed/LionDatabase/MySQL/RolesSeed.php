<?php

declare(strict_types=1);

namespace Database\Seed\LionDatabase\MySQL;

use Database\Factory\LionDatabase\MySQL\RolesFactory;
use Lion\Database\Drivers\MySQL as DB;

class RolesSeed
{
    const COLUMNS = ['roles_name', 'roles_description'];

	/**
	 * Seed the application's database
	 **/
	public function run(): object
	{
        return DB::table('roles')
            ->bulk(self::COLUMNS, RolesFactory::definition())
            ->execute();
	}
}
