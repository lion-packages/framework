<?php

declare(strict_types=1);

use Lion\Bundle\Interface\Migrations\StoreProcedureInterface;
use Lion\Database\Drivers\MySQL;
use Lion\Database\Drivers\Schema\MySQL as Schema;

/**
 * Update roles
 */
return new class implements StoreProcedureInterface
{
    /**
     * {@inheritdoc}
     * */
    public function up(): stdClass
    {
        return Schema::connection(env('DB_NAME', 'lion_database'))
            ->createStoreProcedure('update_roles', function (): void {
                Schema::in()->varchar('_roles_name', 25);
                Schema::in()->varchar('_roles_description', 30);
                Schema::in()->int('_idroles');
            }, function (MySQL $db): void {
                $db->table('roles')
                    ->update([
                        'roles_name' => '_roles_name',
                        'roles_description' => '_roles_description',
                    ])
                    ->where()->equalTo('idroles', '_idroles');
            })
            ->execute();
    }
};
