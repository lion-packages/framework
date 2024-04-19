<?php

declare(strict_types=1);

namespace Database\Factory\LionDatabase\MySQL;

use Lion\Bundle\Enums\TaskStatusEnum;
use Lion\Bundle\Interface\FactoryInterface;

/**
 * Description of the factory 'TaskQueueFactory'
 *
 * @package Database\Factory\LionDatabase\MySQL
 */
class TaskQueueFactory implements FactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public static function columns(): array
    {
        return [
            'task_queue_type',
            'task_queue_data',
            'task_queue_status',
            'task_queue_attempts',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function definition(): array
    {
        return [
            [
                'example',
                json(['template' => 'example', 'message' => 'example']),
                TaskStatusEnum::PENDING->value,
                0
            ]
        ];
    }
}
