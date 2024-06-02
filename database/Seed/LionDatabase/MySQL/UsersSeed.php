<?php

declare(strict_types=1);

namespace Database\Seed\LionDatabase\MySQL;

use Database\Factory\LionDatabase\MySQL\UsersFactory;
use Lion\Bundle\Interface\SeedInterface;
use Lion\Database\Drivers\MySQL as DB;
use stdClass;

/**
 * Seed for users
 *
 * @package Database\Seed\LionDatabase\MySQL
 */
class UsersSeed implements SeedInterface
{
    /**
     * [Index number for seed execution priority]
     *
     * @const INDEX
     */
    const INDEX = 3;

    /**
     * {@inheritdoc}
     **/
    public function run(): stdClass
    {
        return DB::table('users')
            ->bulk(UsersFactory::columns(), UsersFactory::definition())
            ->execute();
    }
}
