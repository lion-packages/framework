<?php

declare(strict_types=1);

namespace Database\Seed\LionDatabase\MySQL;

use Database\Factory\LionDatabase\MySQL\DocumentTypesFactory;
use Lion\Bundle\Interface\SeedInterface;
use Lion\Database\Drivers\MySQL as DB;

class DocumentTypesSeed implements SeedInterface
{
    const INDEX = 1;
    const COLUMNS = ['document_types_name'];

	/**
	 * {@inheritdoc}
	 **/
	public function run(): object
	{
		return DB::table('document_types')
            ->bulk(self::COLUMNS, DocumentTypesFactory::definition())
            ->execute();
	}
}
