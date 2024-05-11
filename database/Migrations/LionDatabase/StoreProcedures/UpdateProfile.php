<?php

declare(strict_types=1);

use Lion\Bundle\Interface\Migrations\StoreProcedureInterface;
use Lion\Database\Drivers\MySQL;
use Lion\Database\Drivers\Schema\MySQL as Schema;

/**
 * Description
 */
return new class implements StoreProcedureInterface
{
    /**
     * {@inheritdoc}
     * */
    public function up(): object
    {
        return Schema::connection(env('DB_NAME', 'lion_database'))
            ->createStoreProcedure('update_profile', function (): void {
                Schema::in()->int('_iddocument_types');
                Schema::in()->varchar('_users_citizen_identification', 25);
                Schema::in()->varchar('_users_name', 25);
                Schema::in()->varchar('_users_last_name', 25);
                Schema::in()->varchar('_users_nickname', 25);
                Schema::in()->int('_idusers');
            }, function (MySQL $db): void {
                $db
                    ->table('users')
                    ->update([
                        'iddocument_types' => '_iddocument_types',
                        'users_citizen_identification' => '_users_citizen_identification',
                        'users_name' => '_users_name',
                        'users_last_name' => '_users_last_name',
                        'users_nickname' => '_users_nickname',
                    ])
                    ->where()->equalTo('idusers', '_idusers');
            })
            ->execute();
    }
};
