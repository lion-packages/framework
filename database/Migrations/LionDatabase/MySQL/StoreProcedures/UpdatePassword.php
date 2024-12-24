<?php

declare(strict_types=1);

namespace Database\Migrations\LionDatabase\MySQL\StoreProcedures;

use Lion\Bundle\Interface\Migrations\StoreProcedureInterface;
use Lion\Database\Drivers\MySQL;
use Lion\Database\Drivers\Schema\MySQL as Schema;
use stdClass;

/**
 * Update password
 *
 * @package Database\Migrations\LionDatabase\MySQL\StoreProcedures
 */
class UpdatePassword implements StoreProcedureInterface
{
    /**
     * {@inheritdoc}
     */
    public function up(): stdClass
    {
        return Schema::connection(env('DB_NAME', 'lion_database'))
            ->createStoreProcedure('update_password', function (): void {
                Schema::in()->blob('_users_password');

                Schema::in()->int('_idusers');
            }, function (MySQL $db): void {
                $db
                    ->table('users')
                    ->update([
                        'users_password' => '_users_password',
                    ])
                    ->where()->equalTo('idusers', '_idusers');
            })
            ->execute();
    }
}
