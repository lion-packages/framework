<?php

declare(strict_types=1);

namespace Database\Seed\LionDatabase\MySQL;

use Database\Factory\LionDatabase\MySQL\TaskQueueFactory;
use Lion\Bundle\Interface\SeedInterface;
use Lion\Database\Drivers\MySQL as DB;

/**
 * Description of 'TaskQueueSeed' Seed
 *
 * @package Database\Seed\LionDatabase\MySQL
 */
class TaskQueueSeed implements SeedInterface
{
    /**
     * [Index number for seed execution priority]
     *
     * @const INDEX
     */
    const INDEX = null;

    /**
     * {@inheritdoc}
     */
    public function run(): object
    {
        return success();

        // return DB::table('task_queue')
        //     ->bulk(TaskQueueFactory::columns(), TaskQueueFactory::definition())
        //     ->execute();
    }
}
