<?php

declare(strict_types=1);

use Lion\Bundle\Interface\Migrations\StoreProcedureInterface;
use Lion\Database\Drivers\MySQL;
use Lion\Database\Drivers\Schema\MySQL as Schema;

/**
 * Update users
 */
return new class implements StoreProcedureInterface
{
    /**
     * {@inheritdoc}
     * */
    public function up(): stdClass
    {
        return Schema::connection(env('DB_NAME', 'lion_database'))
            ->createStoreProcedure('update_users', function (): void {
                Schema::in()->int('_idroles')->null();
                Schema::in()->int('_iddocument_types')->null();
                Schema::in()->varchar('_users_citizen_identification', 25)->null();
                Schema::in()->varchar('_users_name', 25)->null();
                Schema::in()->varchar('_users_last_name', 25)->null();
                Schema::in()->varchar('_users_nickname', 25)->null();
                Schema::in()->varchar('_users_email', 255);
                Schema::in()->int('_idusers');
            }, function (MySQL $db): void {
                $db
                    ->table('users')
                    ->update([
                        'idroles' => '_idroles',
                        'iddocument_types' => '_iddocument_types',
                        'users_citizen_identification' => '_users_citizen_identification',
                        'users_name' => '_users_name',
                        'users_last_name' => '_users_last_name',
                        'users_nickname' => '_users_nickname',
                        'users_email' => '_users_email',
                    ])
                    ->where()->equalTo('idusers', '_idusers');
            })
            ->execute();
    }
};
