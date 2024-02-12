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
			->createStoreProcedure('create_document_types', function() {
				Schema::in()->varchar('_document_types_name', 22);
			}, function(MySQL $db) {
				$db->table('document_types_name')->insert(['document_types_name' => '_document_types_name']);
			})
			->execute();
	}
};
