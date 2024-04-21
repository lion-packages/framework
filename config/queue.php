<?php

declare(strict_types=1);

use App\Html\Email\VerifyAccountHtml;
use Lion\Bundle\Enums\LogTypeEnum;
use Lion\Bundle\Enums\TaskStatusEnum;
use Lion\Bundle\Helpers\Commands\Schedule\TaskQueue;
use Lion\Mailer\Mailer;
use Lion\Mailer\Priority;

/**
 * -----------------------------------------------------------------------------
 * Queued Tasks
 * -----------------------------------------------------------------------------
 * This is where you can register the processes required for your queued tasks
 * -----------------------------------------------------------------------------
 **/

TaskQueue::add(
    'send:email:account-verifify',
    (
        /**
         * Send emails for account validation
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
                /** @var VerifyAccountHtml $htmlTemplate */
                $htmlTemplate = new $data->template;

                Mailer::account(env('MAIL_NAME'))
                    ->subject('Registration Confirmation - Please Verify Your Email')
                    ->from(env('MAIL_USER_NAME'), 'Lion-Packages')
                    ->addAddress($data->account)
                    ->body($htmlTemplate->template()->replace('{{CODE_REPLACE}}', $data->code)->get())
                    ->priority(Priority::HIGH)
                    ->send();
            } catch (Exception $e) {
                TaskQueue::edit($queue, TaskStatusEnum::FAILED);

                logger($e->getMessage(), LogTypeEnum::ERROR->value, [
                    'idtask_queue' => $queue->idtask_queue,
                    'task_queue_type' => $queue->task_queue_type,
                    'task_queue_data' => $queue->task_queue_data
                ], false);
            }
        }
    )
);
