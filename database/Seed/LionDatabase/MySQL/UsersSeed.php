<?php

declare(strict_types=1);

namespace Database\Seed\LionDatabase\MySQL;

use Database\Factory\LionDatabase\MySQL\UsersFactory;
use Lion\Bundle\Interface\SeedInterface;
use Lion\Database\Drivers\MySQL as DB;

/**
 * Seed for users
 *
 * @package Database\Seed\LionDatabase\MySQL
 */
class UsersSeed implements SeedInterface
{
    /**
     * [Index number for seed execution priority]
     *
     * @const INDEX
     */
    const INDEX = 3;

    /**
     * [Entity columns]
     *
     * @const COLUMNS
     */
    const COLUMNS = [
        'idroles',
        'iddocument_types',
        'users_name',
        'users_last_name',
        'users_email',
        'users_password',
        'users_code'
    ];

	/**
	 * {@inheritdoc}
	 **/
	public function run(): object
	{
		return DB::table('users')
            ->bulk(self::COLUMNS, UsersFactory::definition())
            ->execute();
	}
}
