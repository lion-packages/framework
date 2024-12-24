<?php

declare(strict_types=1);

namespace Database\Migrations\LionDatabase\MySQL\Tables;

use Lion\Bundle\Interface\Migrations\TableInterface;
use Lion\Database\Drivers\Schema\MySQL as DB;
use stdClass;

/**
 * Document type scheme
 *
 * @package Database\Migrations\LionDatabase\MySQL\Tables
 */
class DocumentTypes implements TableInterface
{
    /**
     * [Index number for seed execution priority]
     *
     * @const INDEX
     */
    public const ?int INDEX = 1;

    /**
     * {@inheritdoc}
     */
    public function up(): stdClass
    {
        return DB::connection(env('DB_NAME', 'lion_database'))
            ->createTable('document_types', function (): void {
                DB::int('iddocument_types')->notNull()->autoIncrement()->primaryKey();

                DB::varchar('document_types_name', 50)->notNull();
            })
            ->execute();
    }
}
