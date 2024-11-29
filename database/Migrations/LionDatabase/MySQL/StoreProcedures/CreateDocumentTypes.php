<?php

declare(strict_types=1);

namespace Database\Migrations\LionDatabase\MySQL\StoreProcedures;

use Lion\Bundle\Interface\Migrations\StoreProcedureInterface;
use Lion\Database\Drivers\MySQL;
use Lion\Database\Drivers\Schema\MySQL as Schema;
use stdClass;

/**
 * Create document types
 *
 * @package Database\Migrations\LionDatabase\MySQL\StoreProcedures
 */
class CreateDocumentTypes implements StoreProcedureInterface
{
    /**
     * {@inheritdoc}
     */
    public function up(): stdClass
    {
        return Schema::connection(env('DB_NAME', 'lion_database'))
            ->createStoreProcedure('create_document_types', function (): void {
                Schema::in()->varchar('_document_types_name', 22);
            }, function (MySQL $db): void {
                $db
                    ->table('document_types_name')
                    ->insert([
                        'document_types_name' => '_document_types_name',
                    ]);
            })
            ->execute();
    }
};
