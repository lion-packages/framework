<?php

declare(strict_types=1);

namespace Database\Migrations\LionDatabase\MySQL\StoreProcedures;

use Lion\Bundle\Interface\Migrations\StoreProcedureInterface;
use Lion\Database\Drivers\MySQL;
use Lion\Database\Drivers\Schema\MySQL as Schema;
use stdClass;

/**
 * Update document types
 *
 * @package Database\Migrations\LionDatabase\MySQL\StoreProcedures
 */
class UpdateDocumentTypes implements StoreProcedureInterface
{
    /**
     * {@inheritdoc}
     */
    public function up(): stdClass
    {
        return Schema::connection(env('DB_NAME', 'lion_database'))
            ->createStoreProcedure('update_document_types', function (): void {
                Schema::in()->varchar('_document_types_name', 22);

                Schema::in()->int('_iddocument_types');
            }, function (MySQL $db): void {
                $db
                    ->table('document_types')
                    ->update([
                        'document_types_name' => '_document_types_name',
                    ])
                    ->where()->equalTo('iddocument_types', '_iddocument_types');
            })
            ->execute();
    }
}
