<?php

declare(strict_types=1);

namespace Database\Seed\LionDatabase\MySQL;

use Database\Factory\LionDatabase\MySQL\UsersFactory;
use Lion\Database\Drivers\MySQL as DB;

class UsersSeed
{
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
	 * Seed the application's database
	 **/
	public function run(): object
	{
		return DB::table('users')
            ->bulk(self::COLUMNS, UsersFactory::definition())
            ->execute();
	}
}
