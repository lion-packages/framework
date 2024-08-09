<?php

declare(strict_types=1);

use Lion\Bundle\Interface\Migrations\StoreProcedureInterface;
use Lion\Database\Drivers\MySQL;
use Lion\Database\Drivers\Schema\MySQL as Schema;

/**
 * Delete users
 */
return new class implements StoreProcedureInterface
{
    /**
     * {@inheritdoc}
     * */
    public function up(): stdClass
    {
        return Schema::connection(env('DB_NAME', 'lion_database'))
            ->createStoreProcedure('delete_users', function (): void {
                Schema::in()->int('_idusers');
            }, function (MySQL $db): void {
                $db
                    ->table('users')
                    ->delete()
                    ->where()->equalTo('idusers', '_idusers');
            })
            ->execute();
    }
};
