<?php

declare(strict_types=1);

namespace Database\Migrations\LionDatabase\MySQL\StoreProcedures;

use Lion\Bundle\Interface\Migrations\StoreProcedureInterface;
use Lion\Database\Drivers\MySQL;
use Lion\Database\Drivers\Schema\MySQL as Schema;
use stdClass;

/**
 * Update 2FA status for users
 *
 * @package Database\Migrations\LionDatabase\MySQL\StoreProcedures
 */
class Update2fa implements StoreProcedureInterface
{
    /**
     * {@inheritdoc}
     */
    public function up(): stdClass
    {
        return Schema::connection(env('DB_NAME', 'lion_database'))
            ->createStoreProcedure('update_2fa', function (): void {
                Schema::in()->tinyInt('_users_2fa', 1);

                Schema::in()->varchar('_users_2fa_secret', 16)->null();

                Schema::in()->int('_idusers');
            }, function (MySQL $db): void {
                $db
                    ->table('users')
                    ->update([
                        'users_2fa' => '_users_2fa',
                        'users_2fa_secret' => '_users_2fa_secret',
                    ])
                    ->where()->equalTo('idusers', '_idusers');
            })
            ->execute();
    }
}
