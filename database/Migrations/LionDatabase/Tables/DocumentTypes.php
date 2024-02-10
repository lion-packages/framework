<?php

declare(strict_types=1);

use Lion\Bundle\Interface\MigrationUpInterface;
use Lion\Bundle\Traits\Migration;
use Lion\Database\Drivers\Schema\MySQL as DB;

return new class implements MigrationUpInterface
{
	use Migration;

	const INDEX = null;

	/**
	 * {@inheritdoc}
	 * */
	public function up(): object
	{
		return DB::connection('lion_database')
			->createTable('document_types', function() {
				DB::int('iddocument_types')->notNull()->autoIncrement()->primaryKey();
                DB::varchar('document_types_name', 22)->notNull();
			})
			->execute();
	}
};
