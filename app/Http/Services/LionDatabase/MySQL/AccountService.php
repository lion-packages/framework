<?php

declare(strict_types=1);

namespace App\Http\Services\LionDatabase\MySQL;

use Exception;
use App\Exceptions\AccountException;
use App\Models\LionDatabase\MySQL\RegistrationModel;
use App\Models\LionDatabase\MySQL\UsersModel;
use Database\Class\LionDatabase\MySQL\Users;
use Lion\Bundle\Helpers\Commands\Schedule\TaskQueue;
use Lion\Request\Http;
use Lion\Request\Status;

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
    public function setUsersModel(UsersModel $usersModel): AccountService
    {
        $this->usersModel = $usersModel;

        return $this;
    }

    /**
     * Check if the recovery code is inactive
     *
     * @param Users $users [Capsule for the 'Users' entity]
     *
     * @return void
     *
     * @throws AccountException [Throws an error if the verification code is
     * already active]
     */
    public function checkRecoveryCodeInactive(Users $users): void
    {
        if (null != $users->getUsersRecoveryCode()) {
            throw new AccountException(
                'a verification code has already been sent to this account',
                Status::ERROR,
                Http::FORBIDDEN
            );
        }
    }

    /**
     * Send a verification email to the user's account adding the process to the
     * task queue
     *
     * @param Users $users [Capsule for the 'Users' entity]
     *
     * @return void
     *
     * @throws Exception
     */
    public function sendVerifyCodeEmail(Users $users): void
    {
        TaskQueue::push('send:email:account-verify', json([
            'account' => $users->getUsersEmail(),
            'code' => $users->getUsersActivationCode(),
        ]));
    }

    /**
     * Send a recovery email to the user's account adding the process to the
     * task queue
     *
     * @param Users $users [Capsule for the 'Users' entity]
     *
     * @return void
     *
     * @throws Exception
     */
    public function sendRecoveryCodeEmail(Users $users): void
    {
        TaskQueue::push('send:email:account-recovery', json([
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
            throw new AccountException('verification code is invalid [ERR-1]', Status::ERROR, Http::FORBIDDEN);
        }

        if ($data->users_recovery_code != $users->getUsersRecoveryCode()) {
            throw new AccountException('verification code is invalid [ERR-2]', Status::ERROR, Http::FORBIDDEN);
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
            throw new AccountException('activation code is invalid [ERR-1]', Status::ERROR, Http::FORBIDDEN);
        }

        if ($data->users_activation_code != $users->getUsersActivationCode()) {
            throw new AccountException('activation code is invalid [ERR-2]', Status::ERROR, Http::FORBIDDEN);
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
            throw new AccountException('verification code is invalid [ERR-3]', Status::ERROR, Http::UNAUTHORIZED);
        }
    }

    /**
     * Update the activation code for a user's account
     *
     * @param Users $users [Capsule for the 'Users' entity]
     *
     * @return void
     *
     * @throws AccountException [If the code does not update]
     */
    public function updateActivationCode(Users $users): void
    {
        $response = $this->usersModel->updateActivationCodeDB($users);

        if (isError($response)) {
            throw new AccountException('verification code is invalid [ERR-3]', Status::ERROR, Http::UNAUTHORIZED);
        }
    }

    /**
     * Valid if an account exists
     *
     * @param RegistrationModel $registrationModel [Validate in the database if
     * the registration and verification are valid]
     * @param Users $users [Capsule for the 'Users' entity]
     *
     * @return void
     *
     * @throws AccountException [If the code does not update]
     */
    public function validateAccountExists(RegistrationModel $registrationModel, Users $users): void
    {
        $cont = $registrationModel->validateAccountExistsDB($users);

        if ($cont->cont === 1 || $cont->cont === "1") {
            throw new AccountException(
                'there is already an account registered with this email',
                Status::ERROR,
                Http::BAD_REQUEST
            );
        }
    }
}
