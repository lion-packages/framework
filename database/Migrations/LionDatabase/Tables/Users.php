<?php

declare(strict_types=1);

use Lion\Bundle\Interface\MigrationUpInterface;
use Lion\Database\Drivers\Schema\MySQL as DB;

return new class implements MigrationUpInterface
{
	const INDEX = 2;

	/**
	 * {@inheritdoc}
	 * */
	public function up(): object
	{
		return DB::connection(env('DB_NAME', 'lion_database'))
			->createTable('users', function() {
				DB::int('idusers')->notNull()->autoIncrement()->primaryKey();
                DB::int('idroles')->notNull()->foreign('roles', 'idroles');
                DB::int('iddocument_types')->notNull()->foreign('document_types', 'iddocument_types');
                DB::varchar('users_name', 25)->notNull();
                DB::varchar('users_last_name', 25)->notNull();
                DB::varchar('users_email', 255)->notNull()->unique();
                DB::blob('users_password')->notNull();
                DB::varchar('users_code', 45)->unique();
			})
			->execute();
	}
};
