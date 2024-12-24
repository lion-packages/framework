<?php

declare(strict_types=1);

namespace Database\Seed\LionDatabase\MySQL;

use Database\Factory\LionDatabase\MySQL\DocumentTypesFactory;
use Lion\Bundle\Interface\SeedInterface;
use Lion\Database\Drivers\MySQL as DB;
use stdClass;

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
    public const ?int INDEX = 1;

    /**
     * {@inheritdoc}
     **/
    public function run(): stdClass
    {
        return DB::table('document_types')
            ->bulk(DocumentTypesFactory::columns(), DocumentTypesFactory::definition())
            ->execute();
    }
}
