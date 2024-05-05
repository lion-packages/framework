<?php

declare(strict_types=1);

namespace App\Http\Services\LionDatabase\MySQL;

use App\Exceptions\AuthenticationException;
use App\Html\Email\VerifyAccountHtml;
use Database\Class\LionDatabase\MySQL\Users;
use Lion\Bundle\Helpers\Commands\Schedule\TaskQueue;
use Lion\Request\Request;

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
        TaskQueue::push('send:email:account-verify', json([
            'template' => VerifyAccountHtml::class,
            'account' => $users->getUsersEmail(),
            'code' => $users->getUsersActivationCode(),
        ]));
    }

    /**
     * Check and validate if the account verification code is correct
     *
     * @param Users $users [Capsule for the 'Users' entity]
     * @param object $data [Account verification code]
     *
     * @return void
     *
     * @throws AuthenticationException [Throws an error if the verification code
     * has no matches]
     */
    public function verifyAccount(Users $users, object $data): void
    {
        if (isSuccess($data)) {
            throw new AuthenticationException('verification code is invalid [ERR-1]', Request::HTTP_FORBIDDEN);
        }

        if ($data->users_activation_code != $users->getUsersActivationCode()) {
            throw new AuthenticationException('verification code is invalid [ERR-2]', Request::HTTP_FORBIDDEN);
        }
    }
}
