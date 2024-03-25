<?php

declare(strict_types=1);

use Lion\Bundle\Interface\MigrationUpInterface;
use Lion\Database\Drivers\Schema\MySQL as DB;

return new class implements MigrationUpInterface
{
	const INDEX = 1;

	/**
	 * {@inheritdoc}
	 * */
	public function up(): object
	{
		return DB::connection(env('DB_NAME', 'lion_database'))
			->createTable('roles', function() {
				DB::int('idroles')->notNull()->autoIncrement()->primaryKey();
                DB::varchar('roles_name', 25)->notNull();
                DB::varchar('roles_description', 30)->null();
			})
			->execute();
	}
};
