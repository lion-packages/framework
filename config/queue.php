<?php

declare(strict_types=1);

use App\Html\Email\VerifyAccountHtml;
use App\Http\Services\LionDatabase\MySQL\AccountService;
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
 */

TaskQueue::add('send:email:account-recovery', [AccountService::class, 'runSendVerificationCodeEmail']);

TaskQueue::add(
    'send:email:account-verify',
    (
        /**
         * Send emails for account validation
         *
         * @param VerifyAccountHtml $verifyAccountHtml
         * @param object $queue [Queued task object]
         * @param string $account [Mail account]
         * @param string $code [Code]
         *
         * @return void
         *
         * @throws Exception [Catch an exception if the process fails]
         */
        function (VerifyAccountHtml $verifyAccountHtml, object $queue, string $account, string $code): void {
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
