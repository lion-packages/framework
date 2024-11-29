<?php

declare(strict_types=1);

namespace Database\Migrations\LionDatabase\MySQL\Views;

use Lion\Bundle\Interface\Migrations\ViewInterface;
use Lion\Database\Drivers\MySQL;
use Lion\Database\Drivers\Schema\MySQL as Schema;
use stdClass;

/**
 * View to read users with their data relationships
 *
 * @package Database\Migrations\LionDatabase\MySQL\Views
 */
class ReadUsers implements ViewInterface
{
    /**
     * {@inheritdoc}
     */
    public function up(): stdClass
    {
        return Schema::connection(env('DB_NAME', 'lion_database'))
            ->createView('read_users', function (MySQL $db): void {
                $db
                    ->table($db->as('users', 'usr'))
                    ->select(
                        $db->getColumn('idusers', 'usr'),
                        $db->getColumn('users_citizen_identification', 'usr'),
                        $db->getColumn('users_name', 'usr'),
                        $db->getColumn('users_last_name', 'usr'),
                        $db->getColumn('users_nickname', 'usr'),
                    );
            })
            ->execute();
    }
};
