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
    public function up(): stdClass
    {
        return Schema::connection(env('DB_NAME', 'lion_database'))
            ->createStoreProcedure('update_2fa', function (): void {
                Schema::in()->tinyInt('_users_2fa', 1);
                Schema::in()->int('_idusers');
            }, function (MySQL $db): void {
                $db
                    ->table('users')
                    ->update([
                        'users_2fa' => '_users_2fa'
                    ])
                    ->where()->equalTo('idusers', '_idusers');
            })
            ->execute();
    }
};
