<?php

declare(strict_types=1);

use Lion\Bundle\Interface\MigrationUpInterface;
use Lion\Database\Drivers\MySQL;
use Lion\Database\Drivers\Schema\MySQL as Schema;

return new class implements MigrationUpInterface
{
    /**
     * {@inheritdoc}
     * */
    public function up(): object
    {
        return Schema::connection(env('DB_NAME', 'lion_database'))
            ->createStoreProcedure('create_users', function () {
                Schema::in()->int('_idroles')->null();
                Schema::in()->int('_iddocument_types')->null();
                Schema::in()->varchar('_users_citizen_identification', 25)->null();
                Schema::in()->varchar('_users_name', 25)->null();
                Schema::in()->varchar('_users_last_name', 25)->null();
                Schema::in()->varchar('_users_email', 255);
                Schema::in()->blob('_users_password');
                Schema::in()->varchar('_users_code', 45);
            }, function (MySQL $db) {
                $db
                    ->table('users')
                    ->insert([
                        'idroles' => '_idroles',
                        'iddocument_types' => '_iddocument_types',
                        'users_citizen_identification' => '_users_citizen_identification',
                        'users_name' => '_users_name',
                        'users_last_name' => '_users_last_name',
                        'users_email' => '_users_email',
                        'users_password' => '_users_password',
                        'users_code' => '_users_code'
                    ]);
            })
            ->execute();
    }
};
