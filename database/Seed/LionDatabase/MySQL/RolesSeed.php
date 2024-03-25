<?php

declare(strict_types=1);

namespace Database\Seed\LionDatabase\MySQL;

use Database\Factory\LionDatabase\MySQL\RolesFactory;
use Lion\Bundle\Interface\SeedInterface;
use Lion\Database\Drivers\MySQL as DB;

/**
 * Seed for roles
 *
 * @package Database\Seed\LionDatabase\MySQL
 */
class RolesSeed implements SeedInterface
{
    /**
     * [Index number for seed execution priority]
     *
     * @const INDEX
     */
    const INDEX = 2;

    /**
     * [Entity columns]
     *
     * @const COLUMNS
     */
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
