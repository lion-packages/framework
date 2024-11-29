<?php

declare(strict_types=1);

namespace Database\Migrations\LionDatabase\MySQL\StoreProcedures;

use Lion\Bundle\Interface\Migrations\StoreProcedureInterface;
use Lion\Database\Drivers\MySQL;
use Lion\Database\Drivers\Schema\MySQL as Schema;
use stdClass;

/**
 * Create roles
 *
 * @package Database\Migrations\LionDatabase\MySQL\StoreProcedures
 */
class CreateRoles implements StoreProcedureInterface
{
    /**
     * {@inheritdoc}
     */
    public function up(): stdClass
    {
        return Schema::connection(env('DB_NAME', 'lion_database'))
            ->createStoreProcedure('create_roles', function (): void {
                Schema::in()->varchar('_roles_name', 25);

                Schema::in()->varchar('_roles_description', 30);
            }, function (MySQL $db): void {
                $db
                    ->table('roles')
                    ->insert([
                        'roles_name' => '_roles_name',
                        'roles_description' => '_roles_description',
                    ]);
            })
            ->execute();
    }
};
