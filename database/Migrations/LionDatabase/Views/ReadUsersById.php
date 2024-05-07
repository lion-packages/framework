<?php

declare(strict_types=1);

use Lion\Bundle\Interface\Migrations\ViewInterface;
use Lion\Database\Drivers\MySQL;
use Lion\Database\Drivers\Schema\MySQL as Schema;

/**
 * View to read a users with their data relations
 */
return new class implements ViewInterface
{
    /**
     * {@inheritdoc}
     * */
    public function up(): object
    {
        return Schema::connection('lion_database')
            ->createView('read_users_by_id', function (MySQL $db): void {
                $db
                    ->table($db->as('users', 'usr'))
                    ->select(
                        $db->getColumn('idusers', 'usr'),
                        $db->getColumn('idroles', 'usr'),
                        $db->getColumn('iddocument_types', 'usr'),
                        $db->getColumn('users_citizen_identification', 'usr'),
                        $db->getColumn('users_name', 'usr'),
                        $db->getColumn('users_last_name', 'usr'),
                        $db->getColumn('users_nickname', 'usr'),
                        $db->getColumn('users_email', 'usr'),
                        $db->getColumn('users_activation_code', 'usr'),
                        $db->getColumn('users_recovery_code', 'usr'),
                        $db->getColumn('users_code', 'usr'),
                        $db->getColumn('roles_name', 'rl'),
                        $db->getColumn('document_types_name', 'dcmt')
                    )
                    ->left()->join(
                        $db->as('roles', 'rl'),
                        $db->getColumn('idroles', 'usr'),
                        $db->getColumn('idroles', 'rl')
                    )
                    ->left()->join(
                        $db->as('document_types', 'dcmt'),
                        $db->getColumn('iddocument_types', 'usr'),
                        $db->getColumn('iddocument_types', 'dcmt')
                    );
            })
            ->execute();
    }
};
