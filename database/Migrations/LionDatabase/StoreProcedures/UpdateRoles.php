<?php

declare(strict_types=1);

use Lion\Bundle\Interface\MigrationUpInterface;
use Lion\Bundle\Traits\Migration;
use Lion\Database\Drivers\MySQL;
use Lion\Database\Drivers\Schema\MySQL as Schema;

return new class implements MigrationUpInterface
{
	use Migration;

	/**
	 * {@inheritdoc}
	 * */
	public function up(): object
	{
		return Schema::connection('lion_database')
			->createStoreProcedure('update_roles', function() {
				Schema::in()->varchar('_roles_name', 25);
                Schema::in()->varchar('_roles_description', 30);
                Schema::in()->int('_idroles');
			}, function(MySQL $db) {
				$db->table('roles')
                    ->update([
                        'roles_name' => '_roles_name',
                        'roles_description' => '_roles_description'
                    ])
                    ->where()->equalTo('idroles', '_idroles');
			})
			->execute();
	}
};
