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
            ->createView('read_users', function (MySQL $db) {
                $db
                    ->table($db->as('users', 'usr'))
                    ->select(
                        $db->getColumn('idusers', 'usr'),
                        $db->getColumn('users_citizen_identification', 'usr'),
                        $db->getColumn('users_name', 'usr'),
                        $db->getColumn('users_last_name', 'usr'),
                    );
            })
            ->execute();
    }
};
