<?php

declare(strict_types=1);

namespace App\Http\Services\LionDatabase\MySQL;

use App\Html\Email\VerifyAccountHtml;
use Database\Class\LionDatabase\MySQL\Users;
use Lion\Bundle\Helpers\Commands\Schedule\TaskQueue;

/**
 * Service that assists the user registration process
 *
 * @package App\Http\Services\LionDatabase\MySQL
 */
class RegistrationService
{
    /**
     * Send a verification email to the user's account adding the process to the
     * task queue
     *
     * @param Users $users [Capsule for the 'Users' entity]
     *
     * @return void
     */
    public function sendVerifiyEmail(Users $users): void
    {
        TaskQueue::push('send:email:account-verifify', json([
            'template' => VerifyAccountHtml::class,
            'account' => $users->getUsersEmail(),
            'code' => $users->getUsersActivationCode()
        ]));
    }
}
