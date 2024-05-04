<?php

declare(strict_types=1);

namespace App\Http\Services\LionDatabase\MySQL;

use App\Exceptions\AccountException;
use App\Html\Email\RecoveryAccountHtml;
use App\Models\LionDatabase\MySQL\UsersModel;
use Database\Class\LionDatabase\MySQL\Users;
use Lion\Bundle\Helpers\Commands\Schedule\TaskQueue;
use Lion\Request\Request;

/**
 * Manage user account processes
 *
 * @property UsersModel $usersModel [Model for the Users entity]
 *
 * @package App\Http\Services\LionDatabase\MySQL
 */
class AccountService
{
    /**
     * [Model for the Users entity]
     *
     * @var UsersModel $usersModel
     */
    private UsersModel $usersModel;

    /**
     * @required
     */
    public function setUsersModel(UsersModel $usersModel): void
    {
        $this->usersModel = $usersModel;
    }

    /**
     * Send a recovery email to the user's account adding the process to the
     * task queue
     *
     * @param Users $users [Capsule for the 'Users' entity]
     *
     * @return void
     */
    public function sendRecoveryCode(Users $users): void
    {
        TaskQueue::push('send:email:account-recovery', json([
            'template' => RecoveryAccountHtml::class,
            'account' => $users->getUsersEmail(),
            'code' => $users->getUsersRecoveryCode(),
        ]));
    }

    /**
     * Verify and validate if the defined account code is correct
     *
     * @param Users $users [Capsule for the 'Users' entity]
     * @param object $data [Account verification code]
     *
     * @return void
     *
     * @throws AccountException [Throws an error if the verification code has no
     * matches]
     */
    public function verifyRecoveryCode(Users $users, object $data): void
    {
        if (isSuccess($data)) {
            throw new AccountException('verification code is invalid [ERR-1]', Request::HTTP_FORBIDDEN);
        }

        if ($data->users_recovery_code != $users->getUsersRecoveryCode()) {
            throw new AccountException('verification code is invalid [ERR-2]', Request::HTTP_FORBIDDEN);
        }
    }

    /**
     * Verify and validate if the defined account code is correct
     *
     * @param Users $users [Capsule for the 'Users' entity]
     * @param object $data [Account verification code]
     *
     * @return void
     *
     * @throws AccountException [Throws an error if the activation code has no
     * matches]
     */
    public function verifyActivationCode(Users $users, object $data): void
    {
        if (isSuccess($data)) {
            throw new AccountException('activation code is invalid [ERR-1]', Request::HTTP_FORBIDDEN);
        }

        if ($data->users_activation_code != $users->getUsersActivationCode()) {
            throw new AccountException('activation code is invalid [ERR-2]', Request::HTTP_FORBIDDEN);
        }
    }

    /**
     * Update the recovery code for a user's account
     *
     * @param Users $users [Capsule for the 'Users' entity]
     *
     * @return void
     *
     * @throws AccountException [If the code does not update]
     */
    public function updateRecoveryCode(Users $users): void
    {
        $response = $this->usersModel->updateRecoveryCodeDB($users);

        if (isError($response)) {
            throw new AccountException('verification code is invalid [ERR-3]', Request::HTTP_UNAUTHORIZED);
        }
    }
}
