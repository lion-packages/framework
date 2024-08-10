<?php

declare(strict_types=1);

use Lion\Bundle\Interface\Migrations\StoreProcedureInterface;
use Lion\Database\Drivers\MySQL;
use Lion\Database\Drivers\Schema\MySQL as Schema;

/**
 * Delete document types
 */
return new class implements StoreProcedureInterface
{
    /**
     * {@inheritdoc}
     * */
    public function up(): stdClass
    {
        return Schema::connection(env('DB_NAME', 'lion_database'))
            ->createStoreProcedure('delete_document_types', function (): void {
                Schema::in()->int('_iddocument_types');
            }, function (MySQL $db): void {
                $db
                    ->table('document_types')
                    ->delete()
                    ->where()->equalTo('iddocument_types', '_iddocument_types');
            })
            ->execute();
    }
};
