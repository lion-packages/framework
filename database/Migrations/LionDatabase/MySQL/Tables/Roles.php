<?php

declare(strict_types=1);

namespace Database\Migrations\LionDatabase\MySQL\Tables;

use Lion\Bundle\Interface\Migrations\TableInterface;
use Lion\Database\Drivers\Schema\MySQL as DB;
use stdClass;

/**
 * Role scheme
 *
 * @package Database\Migrations\LionDatabase\MySQL\Tables
 */
class Roles implements TableInterface
{
    /**
     * [Index number for seed execution priority]
     *
     * @const INDEX
     */
    public const ?int INDEX = 2;

    /**
     * {@inheritdoc}
     */
    public function up(): stdClass
    {
        return DB::connection(env('DB_NAME', 'lion_database'))
            ->createTable('roles', function (): void {
                DB::int('idroles')->notNull()->autoIncrement()->primaryKey();

                DB::varchar('roles_name', 25)->notNull();

                DB::varchar('roles_description', 30)->null();
            })
            ->execute();
    }
}
