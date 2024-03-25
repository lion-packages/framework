<?php

declare(strict_types=1);

use Lion\Bundle\Interface\MigrationUpInterface;
use Lion\Database\Drivers\MySQL;
use Lion\Database\Drivers\Schema\MySQL as Schema;

return new class implements MigrationUpInterface
{
	/**
	 * {@inheritdoc}
	 * */
	public function up(): object
	{
		return Schema::connection(env('DB_NAME', 'lion_database'))
			->createStoreProcedure('create_roles', function() {
				Schema::in()->varchar('_roles_name', 25);
                Schema::in()->varchar('_roles_description', 30);
			}, function(MySQL $db) {
				$db
                    ->table('roles')
                    ->insert([
                        'roles_name' => '_roles_name',
                        'roles_description' => '_roles_description'
                    ]);
			})
			->execute();
	}
};
