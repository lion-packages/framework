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
			->createStoreProcedure('update_document_types', function() {
				Schema::in()->varchar('_document_types_name', 22);
                Schema::in()->int('_iddocument_types');
			}, function(MySQL $db) {
				$db
                    ->table('document_types')
                    ->update(['document_types_name' => '_document_types_name'])
                    ->where()->equalTo('iddocument_types', '_iddocument_types');
			})
			->execute();
	}
};
