<?php

declare(strict_types=1);

namespace Database\Migrations\LionDatabase\MySQL\Tables;

use Lion\Bundle\Interface\Migrations\TableInterface;
use Lion\Database\Drivers\Schema\MySQL as Schema;
use stdClass;

/**
 * Description
 *
 * @package Database\Migrations\LionDatabase\MySQL\Tables
 */
class Users implements TableInterface
{
    /**
     * [Index number for seed execution priority]
     *
     * @const INDEX
     */
    public const ?int INDEX = null;

    /**
     * {@inheritdoc}
     */
    public function up(): stdClass
    {
        return Schema::connection(env('DB_DEFAULT', 'local'))
            ->createTable('users', function (): void {
                Schema::int('idusers')
                    ->notNull()
                    ->autoIncrement()
                    ->primaryKey();

                Schema::varchar('users_name', 50)
                    ->notNull()
                    ->unique();

                Schema::dateTime('created_at');
            })
            ->execute();
    }
}
