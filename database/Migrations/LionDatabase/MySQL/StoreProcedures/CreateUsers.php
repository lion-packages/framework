<?php

declare(strict_types=1);

namespace Database\Migrations\LionDatabase\MySQL\StoreProcedures;

use Lion\Bundle\Interface\Migrations\StoreProcedureInterface;
use Lion\Database\Drivers\MySQL;
use Lion\Database\Drivers\Schema\MySQL as Schema;
use stdClass;

/**
 * Create users
 *
 * @package Database\Migrations\LionDatabase\MySQL\StoreProcedures
 */
class CreateUsers implements StoreProcedureInterface
{
    /**
     * {@inheritdoc}
     */
    public function up(): stdClass
    {
        return Schema::connection(env('DB_NAME', 'lion_database'))
            ->createStoreProcedure('create_users', function (): void {
                Schema::in()->int('_idroles')->null();

                Schema::in()->int('_iddocument_types')->null();

                Schema::in()->varchar('_users_citizen_identification', 25)->null();

                Schema::in()->varchar('_users_name', 25)->null();

                Schema::in()->varchar('_users_last_name', 25)->null();

                Schema::in()->varchar('_users_nickname', 25)->null();

                Schema::in()->varchar('_users_email', 255);

                Schema::in()->blob('_users_password');

                Schema::in()->varchar('_users_activation_code', 6);

                Schema::in()->varchar('_users_recovery_code', 6)->null();

                Schema::in()->varchar('_users_code', 18);

                Schema::in()->tinyInt('_users_2fa', 1);

                Schema::in()->varchar('_users_2fa_secret', 16)->null();
            }, function (MySQL $db): void {
                $db
                    ->table('users')
                    ->insert([
                        'idroles' => '_idroles',
                        'iddocument_types' => '_iddocument_types',
                        'users_citizen_identification' => '_users_citizen_identification',
                        'users_name' => '_users_name',
                        'users_last_name' => '_users_last_name',
                        'users_nickname' => '_users_nickname',
                        'users_email' => '_users_email',
                        'users_password' => '_users_password',
                        'users_activation_code' => '_users_activation_code',
                        'users_recovery_code' => '_users_recovery_code',
                        'users_code' => '_users_code',
                        'users_2fa' => '_users_2fa',
                        'users_2fa_secret' => '_users_2fa_secret',
                    ]);
            })
            ->execute();
    }
};
