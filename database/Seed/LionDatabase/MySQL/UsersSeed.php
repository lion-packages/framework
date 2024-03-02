<?php

declare(strict_types=1);

namespace Database\Seed\LionDatabase\MySQL;

use Database\Factory\LionDatabase\MySQL\UsersFactory;
use Lion\Bundle\Interface\SeedInterface;
use Lion\Database\Drivers\MySQL as DB;

class UsersSeed implements SeedInterface
{
    const INDEX = 3;
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
