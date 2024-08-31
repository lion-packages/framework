<?php

declare(strict_types=1);

use App\Http\Services\LionDatabase\MySQL\AccountService;
use Lion\Bundle\Helpers\Commands\Schedule\TaskQueue;

/**
 * -----------------------------------------------------------------------------
 * Queued Tasks
 * -----------------------------------------------------------------------------
 * This is where you can register the processes required for your queued tasks
 * -----------------------------------------------------------------------------
 */

TaskQueue::add('send:email:account-recovery', [AccountService::class, 'runSendVerificationCodeEmail']);

TaskQueue::add('send:email:account-verify', [AccountService::class, 'runSendRecoveryCodeByEmail']);
