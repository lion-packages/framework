<?php

declare(strict_types=1);

namespace App\Http\Controllers\LionDatabase\MySQL;

use App\Http\Services\JWTService;
use App\Http\Services\LionDatabase\MySQL\AccountService;
use App\Http\Services\LionDatabase\MySQL\LoginService;
use App\Http\Services\LionDatabase\MySQL\PasswordManagerService;
use App\Models\LionDatabase\MySQL\PasswordManagerModel;
use App\Models\LionDatabase\MySQL\UsersModel;
use Database\Class\LionDatabase\MySQL\Users;
use Database\Class\PasswordManager;
use Lion\Security\Validation;

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
     * @param Users $users [Capsule for the 'Users' entity]
     * @param UsersModel $usersModel [Model for the Users entity]
     * @param AccountService $accountService [Manage user account processes]
     * @param LoginService $loginService [Allows you to manage the user
     * authentication process]
     *
     * @return object
     */
    public function recoveryPassword(
        Users $users,
        UsersModel $usersModel,
        AccountService $accountService,
        LoginService $loginService
    ): object {
        $loginService->validateSession($users->setUsersEmail(request->users_email));

        $user = $usersModel->readUsersByEmailDB($users);

        $users
            ->setIdusers($user->idusers)
            ->setUsersRecoveryCode(fake()->numerify('######'));

        $accountService->updateRecoveryCode($users);

        $accountService->sendRecoveryCode($users);

        return success('confirmation code sent, check your email inbox to see your verification code');
    }

    /**
     * Manage system password recovery
     *
     * @param PasswordManager $passwordManager [Capsule for the
     * 'PasswordManager' entity]
     * @param PasswordManagerModel $passwordManagerModel [Password management
     * model]
     * @param PasswordManagerService $passwordManagerService [Manage different
     * processes for strong password verifications]
     * @param JWTService $jWTService [Service to manipulate JWT tokens]
     * @param Validation $validation [Allows you to validate form data and
     * generate encryption safely]
     *
     * @return object
     */
    public function updatePassword(
        PasswordManager $passwordManager,
        PasswordManagerModel $passwordManagerModel,
        PasswordManagerService $passwordManagerService,
        JWTService $jWTService,
        Validation $validation
    ): object {
        $data = $jWTService->getTokenData(storage_path(env('RSA_URL_PATH')));

        $users = $passwordManagerModel->getPasswordDB(
            $passwordManager
                ->capsule()
                ->setIdusers($data->idusers)
        );

        $passwordManagerService->verifyPasswords(
            $users->users_password,
            $passwordManager->getUsersPassword()
        );

        $passwordManagerService->comparePasswords(
            $passwordManager->getUsersPasswordNew(),
            $passwordManager->getUsersPasswordConfirm()
        );

        $passwordManagerModel->updatePasswordDB(
            $passwordManager
                ->setUsersPasswordConfirm($validation->passwordHash($passwordManager->getUsersPasswordConfirm()))
        );

        return success('password updated successfully');
    }
}
