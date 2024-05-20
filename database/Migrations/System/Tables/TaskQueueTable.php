<?php

declare(strict_types=1);

use Lion\Bundle\Enums\TaskStatusEnum;
use Lion\Bundle\Interface\Migrations\TableInterface;
use Lion\Database\Drivers\Schema\MySQL as Schema;
use Lion\Database\Helpers\Constants\MySQLConstants;

/**
 * Generate the schema for the queued tasks
 */
return new class implements TableInterface
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
    public function up(): object
    {
        return Schema::connection(env('DB_NAME', 'lion_database'))
            ->createTable('task_queue', function () {
                Schema::int('idtask_queue')->notNull()->autoIncrement()->primaryKey();
                Schema::varchar('task_queue_type', 255)->notNull();
                Schema::json('task_queue_data')->notNull();

                Schema::enum('task_queue_status', TaskStatusEnum::values())
                    ->notNull()
                    ->default(TaskStatusEnum::PENDING->value);

                Schema::int('task_queue_attempts', 11)->notNull();
                Schema::timeStamp('task_queue_create_at')->default(MySQLConstants::CURRENT_TIMESTAMP);
            })
            ->execute();
    }
};
