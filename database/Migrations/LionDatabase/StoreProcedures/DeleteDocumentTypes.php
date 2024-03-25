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
			->createStoreProcedure('delete_document_types', function() {
				Schema::in()->int('_iddocument_types');
			}, function(MySQL $db) {
				$db->table('document_types')->delete()->where()->equalTo('iddocument_types', '_iddocument_types');
			})
			->execute();
	}
};
