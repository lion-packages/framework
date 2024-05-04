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
            ->createStoreProcedure('update_recovery_code', function () {
                Schema::in()->varchar('_users_recovery_code', 6);
                Schema::in()->int('_idusers');
            }, function (MySQL $db) {
                $db
                    ->table('users')
                    ->update([
                        'users_recovery_code' => '_users_recovery_code'
                    ])
                    ->where()->equalTo('idusers', '_idusers');
            })
            ->execute();
    }
};
