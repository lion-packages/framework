<?php

declare(strict_types=1);

namespace Database\Seed\LionDatabase\MySQL;

use Database\Factory\LionDatabase\MySQL\DocumentTypesFactory;
use Lion\Database\Drivers\MySQL as DB;

class DocumentTypesSeed
{
    const COLUMNS = ['document_types_name'];

	/**
	 * Seed the application's database
	 **/
	public function run(): object
	{
		return DB::table('document_types')
            ->bulk(self::COLUMNS, DocumentTypesFactory::definition())
            ->execute();
	}
}
