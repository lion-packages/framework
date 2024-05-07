<?php

declare(strict_types=1);

use Lion\Bundle\Interface\Migrations\TableInterface;
use Lion\Database\Drivers\Schema\MySQL as DB;

/**
 * Document type scheme
 */
return new class implements TableInterface
{
    /**
     * [Index number for seed execution priority]
     *
     * @const INDEX
     */
    const INDEX = 1;

    /**
     * {@inheritdoc}
     * */
    public function up(): object
    {
        return DB::connection('lion_database')
            ->createTable('document_types', function (): void {
                DB::int('iddocument_types')->notNull()->autoIncrement()->primaryKey();
                DB::varchar('document_types_name', 50)->notNull();
            })
            ->execute();
    }
};
