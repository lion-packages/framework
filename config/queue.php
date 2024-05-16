<?php

declare(strict_types=1);

use App\Html\Email\RecoveryAccountHtml;
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
    'send:email:account-recovery',
    /**
     * Send emails for account validation
     *
     * @param RecoveryAccountHtml $recoveryAccountHtml [Password recovery
     * template]
     * @param object $queue [Queued task object]
     *
     * @return void
     *
     * @throws Exception [Catch an exception if the process fails]
     */
    function (RecoveryAccountHtml $recoveryAccountHtml, object $queue, string $account, string $code): void {
        try {
            Mailer::account(env('MAIL_NAME'))
                ->subject('Password Recovery: check your email')
                ->from(env('MAIL_USER_NAME'), 'Lion-Packages')
                ->addAddress($account)
                ->body(
                    $recoveryAccountHtml
                        ->template()
                        ->replace('CODE_REPLACE', $code)
                        ->get()
                )
                ->priority(Priority::HIGH)
                ->send();
        } catch (Exception $e) {
            TaskQueue::edit($queue, TaskStatusEnum::FAILED);

            logger($e->getMessage(), LogTypeEnum::ERROR, [
                'idtask_queue' => $queue->idtask_queue,
                'task_queue_type' => $queue->task_queue_type,
                'task_queue_data' => $queue->task_queue_data
            ], false);
        }
    }
);

TaskQueue::add(
    'send:email:account-verify',
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
        function (VerifyAccountHtml $verifyAccountHtml, object $queue, string $code, string $account): void {
            try {
                Mailer::account(env('MAIL_NAME'))
                    ->subject('Registration Confirmation - Please Verify Your Email')
                    ->from(env('MAIL_USER_NAME'), 'Lion-Packages')
                    ->addAddress($account)
                    ->body(
                        $verifyAccountHtml
                            ->template()
                            ->replace('CODE_REPLACE', $code)
                            ->get()
                    )
                    ->priority(Priority::HIGH)
                    ->send();
            } catch (Exception $e) {
                TaskQueue::edit($queue, TaskStatusEnum::FAILED);

                logger($e->getMessage(), LogTypeEnum::ERROR, [
                    'idtask_queue' => $queue->idtask_queue,
                    'task_queue_type' => $queue->task_queue_type,
                    'task_queue_data' => $queue->task_queue_data
                ], false);
            }
        }
    )
);
