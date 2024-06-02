<?php

declare(strict_types=1);

use Lion\Bundle\Interface\Migrations\TableInterface;
use Lion\Database\Drivers\Schema\MySQL as DB;

/**
 * User scheme
 */
return new class implements TableInterface
{
    /**
     * [Index number for seed execution priority]
     *
     * @const INDEX
     */
    const INDEX = 3;

    /**
     * {@inheritdoc}
     * */
    public function up(): stdClass
    {
        return DB::connection('lion_database')
            ->createTable('users', function (): void {
                DB::int('idusers')->notNull()->autoIncrement()->primaryKey();
                DB::int('idroles')->null()->foreign('roles', 'idroles');
                DB::int('iddocument_types')->null()->foreign('document_types', 'iddocument_types');
                DB::varchar('users_citizen_identification', 25)->null()->unique();
                DB::varchar('users_name', 25)->null();
                DB::varchar('users_last_name', 25)->null();
                DB::varchar('users_nickname', 25)->null();
                DB::varchar('users_email', 255)->notNull()->unique();
                DB::blob('users_password')->notNull();
                DB::varchar('users_activation_code', 6)->null();
                DB::varchar('users_recovery_code', 6)->null();
                DB::varchar('users_code', 18)->notNull()->unique();
            })
            ->execute();
    }
};
