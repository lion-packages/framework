<?php

declare(strict_types=1);

namespace App\Http\Services\LionDatabase\MySQL;

use App\Exceptions\AuthenticationException;
use Database\Class\LionDatabase\MySQL\Users;
use Lion\Request\Http;
use Lion\Request\Status;

/**
 * Service that assists the user registration process
 *
 * @package App\Http\Services\LionDatabase\MySQL
 */
class RegistrationService
{
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
            throw new AuthenticationException(
                'verification code is invalid [ERR-1]',
                Status::SESSION_ERROR,
                Http::FORBIDDEN
            );
        }

        if ($data->users_activation_code != $users->getUsersActivationCode()) {
            throw new AuthenticationException(
                'verification code is invalid [ERR-2]',
                Status::SESSION_ERROR,
                Http::FORBIDDEN
            );
        }
    }
}
