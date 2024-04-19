<?php

declare(strict_types=1);

use Lion\Bundle\Enums\LogTypeEnum;
use Lion\Bundle\Enums\TaskStatusEnum;
use Lion\Bundle\Helpers\Commands\Schedule\TaskQueue;

/**
 * -----------------------------------------------------------------------------
 * Queued Tasks
 * -----------------------------------------------------------------------------
 * This is where you can register the processes required for your queued tasks
 * -----------------------------------------------------------------------------
 **/

TaskQueue::add(
    'example',
    (
        /**
         * [Description]
         *
         * @param object $queue [Queued task object]
         *
         * @return void
         *
         * @throws Exception [Catch an exception if the process fails]
         */
        function (object $queue): void {
            $data = (object) json_decode($queue->task_queue_data, true);

            try {
                // ...
            } catch (Exception $e) {
                TaskQueue::edit($queue, TaskStatusEnum::FAILED);

                logger($e->getMessage(), LogTypeEnum::ERROR->value, [
                    'idtask_queue' => $queue->idtask_queue,
                    'task_queue_type' => $queue->task_queue_type,
                    'task_queue_data' => $queue->task_queue_data
                ]);
            }
        }
    )
);
