<?php

declare(strict_types=1);

namespace Database\Seed\LionDatabase\MySQL;

use Database\Factory\LionDatabase\MySQL\DocumentTypesFactory;
use Lion\Bundle\Interface\SeedInterface;
use Lion\Database\Drivers\MySQL as DB;

/**
 * Seed for document types
 *
 * @package Database\Seed\LionDatabase\MySQL
 */
class DocumentTypesSeed implements SeedInterface
{
    /**
     * [Index number for seed execution priority]
     *
     * @const INDEX
     */
    const INDEX = 1;

    /**
     * [Entity columns]
     *
     * @const COLUMNS
     */
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
