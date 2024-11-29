<?php

declare(strict_types=1);

namespace Database\Migrations\LionDatabase\MySQL\StoreProcedures;

use Lion\Bundle\Interface\Migrations\StoreProcedureInterface;
use Lion\Database\Drivers\MySQL;
use Lion\Database\Drivers\Schema\MySQL as Schema;
use stdClass;

/**
 * Update recovery code
 *
 * @package Database\Migrations\LionDatabase\MySQL\StoreProcedures
 */
class UpdateRecoveryCode implements StoreProcedureInterface
{
    /**
     * {@inheritdoc}
     */
    public function up(): stdClass
    {
        return Schema::connection(env('DB_NAME', 'lion_database'))
            ->createStoreProcedure('update_recovery_code', function (): void {
                Schema::in()->varchar('_users_recovery_code', 6);

                Schema::in()->int('_idusers');
            }, function (MySQL $db): void {
                $db
                    ->table('users')
                    ->update([
                        'users_recovery_code' => '_users_recovery_code',
                    ])
                    ->where()->equalTo('idusers', '_idusers');
            })
            ->execute();
    }
};
