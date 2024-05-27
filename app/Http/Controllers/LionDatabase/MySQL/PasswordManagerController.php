<?php

declare(strict_types=1);

namespace App\Http\Controllers\LionDatabase\MySQL;

use App\Exceptions\AccountException;
use App\Exceptions\AuthenticationException;
use App\Exceptions\PasswordException;
use App\Http\Services\AESService;
use App\Http\Services\JWTService;
use App\Http\Services\LionDatabase\MySQL\AccountService;
use App\Http\Services\LionDatabase\MySQL\LoginService;
use App\Http\Services\LionDatabase\MySQL\PasswordManagerService;
use App\Models\LionDatabase\MySQL\PasswordManagerModel;
use App\Models\LionDatabase\MySQL\UsersModel;
use Database\Class\LionDatabase\MySQL\Users;
use Database\Class\PasswordManager;

/**
 * Driver to manage passwords
 *
 * @package App\Http\Controllers\LionDatabase\MySQL
 */
class PasswordManagerController
{
    /**
     * Manage user password recovery by sending a verification email
     *
     * @route /api/auth/password/recovery
     *
     * @param Users $users [Capsule for the 'Users' entity]
     * @param UsersModel $usersModel [Model for the Users entity]
     * @param AccountService $accountService [Manage user account processes]
     * @param LoginService $loginService [Allows you to manage the user
     * authentication process]
     *
     * @return object
     *
     * @throws AuthenticationException
     * @throws AccountException
     */
    public function recoveryPassword(
        Users $users,
        UsersModel $usersModel,
        AccountService $accountService,
        LoginService $loginService
    ): object {
        $users
            ->setUsersEmail(request('users_email'));

        $loginService->validateSession($users);

        $user = $usersModel->readUsersByEmailDB($users);

        $users
            ->setUsersRecoveryCode($user->users_recovery_code);

        $accountService->checkRecoveryCodeInactive($users);

        $users
            ->setIdusers($user->idusers)
            ->setUsersRecoveryCode(fake()->numerify('######'));

        $accountService->updateRecoveryCode($users);

        $accountService->sendRecoveryCodeEmail($users);

        return success('confirmation code sent, check your email inbox to see your verification code');
    }

    /**
     * Update lost user passwords
     *
     * @route /api/auth/password/verify-code
     *
     * @param Users $users [Capsule for the 'Users' entity]
     * @param PasswordManager $passwordManager [Capsule for the
     * 'PasswordManager' entity]
     * @param UsersModel $usersModel [Model for the Users entity]
     * @param PasswordManagerModel $passwordManagerModel [Password management
     * model]
     * @param AccountService $accountService [Manage user account processes]
     * @param PasswordManagerService $passwordManagerService [Manage different
     * processes for strong password verifications]
     *
     * @return object
     *
     * @throws AuthenticationException
     * @throws AccountException
     * @throws PasswordException
     */
    public function updateLostPassword(
        Users $users,
        PasswordManager $passwordManager,
        UsersModel $usersModel,
        PasswordManagerModel $passwordManagerModel,
        AccountService $accountService,
        PasswordManagerService $passwordManagerService,
        LoginService $loginService
    ): object {
        $users->capsule();

        $loginService->validateSession($users);

        $data = $usersModel->readUsersByEmailDB($users);

        $accountService->verifyRecoveryCode($users, $data);

        $passwordManager->capsule();

        $passwordManagerService->comparePasswords(
            $passwordManager->getUsersPasswordNew(),
            $passwordManager->getUsersPasswordConfirm()
        );

        $passwordManagerService->updatePassword(
            $passwordManagerModel,
            $passwordManager
                ->setIdusers($data->idusers)
                ->setUsersPasswordConfirm(request('users_password_confirm'))
        );

        $accountService->updateRecoveryCode(
            $users
                ->setIdusers($data->idusers)
                ->setUsersRecoveryCode(null)
        );

        return success('the recovery code is valid, your password has been updated successfully');
    }

    /**
     * Update user passwords
     *
     * @route /api/profile/password
     *
     * @param PasswordManager $passwordManager [Capsule for the
     * 'PasswordManager' entity]
     * @param PasswordManagerModel $passwordManagerModel [Password management
     * model]
     * @param PasswordManagerService $passwordManagerService [Manage different
     * processes for strong password verifications]
     * @param JWTService $jWTService [Service to manipulate JWT tokens]
     * @param AESService $aESService [Encrypt and decrypt data with AES]
     *
     * @return object
     *
     * @throws PasswordException
     */
    public function updatePassword(
        PasswordManager $passwordManager,
        PasswordManagerModel $passwordManagerModel,
        PasswordManagerService $passwordManagerService,
        JWTService $jWTService,
        AESService $aESService,
    ): object {
        $data = $jWTService->getTokenData(env('RSA_URL_PATH'));

        $decode = $aESService->decode(['idusers' => $data->idusers]);

        $users = $passwordManagerModel->getPasswordDB(
            $passwordManager
                ->capsule()
                ->setIdusers((int) $decode['idusers'])
        );

        $passwordManagerService->verifyPasswords($users->users_password, $passwordManager->getUsersPassword());

        $passwordManagerService->comparePasswords(
            $passwordManager->getUsersPasswordNew(),
            $passwordManager->getUsersPasswordConfirm()
        );

        $passwordManagerService->updatePassword($passwordManagerModel, $passwordManager);

        return success('password updated successfully');
    }
}
