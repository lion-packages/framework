<?php

declare(strict_types=1);

namespace Database\Seed;

use Database\Factory\UsersFactory;
use Lion\Bundle\Interface\SeedInterface;
use Lion\Database\Drivers\MySQL as DB;
use stdClass;

/**
 * Description of 'UsersSeed' Seed
 *
 * @package Database\Seed
 */
class UsersSeed implements SeedInterface
{
    /**
     * [Index number for seed execution priority]
     *
     * @const INDEX
     */
    const ?int INDEX = null;

    /**
     * {@inheritdoc}
     */
    public function run(): stdClass
    {
        return DB::connection(env('DB_DEFAULT', 'local'))
            ->table('users')
            ->bulk(UsersFactory::columns(), UsersFactory::definition())
            ->execute();
    }
}
