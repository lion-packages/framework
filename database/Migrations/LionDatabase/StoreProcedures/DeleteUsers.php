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
			->createStoreProcedure('delete_users', function() {
				Schema::in()->int('_idusers');
			}, function(MySQL $db) {
				$db->table('users')->delete()->where()->equalTo('idusers', '_idusers');
			})
			->execute();
	}
};
