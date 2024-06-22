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
use App\Rules\LionDatabase\MySQL\Users\UsersEmailRule;
use App\Rules\LionDatabase\MySQL\Users\UsersPasswordRule;
use App\Rules\LionDatabase\MySQL\Users\UsersRecoveryCodeRequiredRule;
use App\Rules\UsersPasswordConfirmRule;
use App\Rules\UsersPasswordNewRule;
use Database\Class\LionDatabase\MySQL\Users;
use Database\Class\PasswordManager;
use Exception;
use Lion\Route\Attributes\Rules;
use stdClass;

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
     * @route /api/auth/recovery/password
     *
     * @param Users $users [Capsule for the 'Users' entity]
     * @param UsersModel $usersModel [Model for the Users entity]
     * @param AccountService $accountService [Manage user account processes]
     * @param LoginService $loginService [Allows you to manage the user
     * authentication process]
     *
     * @return stdClass
     *
     * @throws AuthenticationException
     * @throws AccountException
     * @throws Exception
     */
    #[Rules(UsersEmailRule::class)]
    public function recoveryPassword(
        Users $users,
        UsersModel $usersModel,
        AccountService $accountService,
        LoginService $loginService
    ): stdClass {
        $users
            ->setUsersEmail(request('users_email'));

        $loginService->validateSession($users);

        /** @var stdClass $user */
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
     * @route /api/auth/recovery/verify-code
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
     * @param LoginService $loginService [Allows you to manage the user
     * authentication process]
     * @var AESService $aESService [Encrypt and decrypt data with AES]
     *
     * @return stdClass
     *
     * @throws AuthenticationException
     * @throws AccountException
     * @throws PasswordException
     */
    #[Rules(
        UsersEmailRule::class,
        UsersRecoveryCodeRequiredRule::class,
        UsersPasswordNewRule::class,
        UsersPasswordConfirmRule::class
    )]
    public function updateLostPassword(
        Users $users,
        PasswordManager $passwordManager,
        UsersModel $usersModel,
        PasswordManagerModel $passwordManagerModel,
        AccountService $accountService,
        PasswordManagerService $passwordManagerService,
        LoginService $loginService,
        AESService $aESService
    ): stdClass {
        $users->capsule();

        $loginService->validateSession($users);

        /** @var stdClass $data */
        $data = $usersModel->readUsersByEmailDB($users);

        $accountService->verifyRecoveryCode($users, $data);

        $passwordManager->capsule();

        $decode = $aESService->decode([
            'users_password_new' => $passwordManager->getUsersPasswordNew(),
            'users_password_confirm' => $passwordManager->getUsersPasswordConfirm(),
        ]);

        $passwordManagerService->comparePasswords(
            $passwordManager
                ->setUsersPasswordNew($decode['users_password_new'])
                ->getUsersPasswordNew(),
            $passwordManager
                ->setUsersPasswordConfirm($decode['users_password_confirm'])
                ->getUsersPasswordConfirm()
        );

        $passwordManagerService->updatePassword(
            $passwordManagerModel,
            $passwordManager
                ->setIdusers($data->idusers)
                ->setUsersPasswordConfirm($decode['users_password_confirm'])
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
     * @return stdClass
     *
     * @throws PasswordException
     */
    #[Rules(UsersPasswordRule::class, UsersPasswordNewRule::class, UsersPasswordConfirmRule::class)]
    public function updatePassword(
        PasswordManager $passwordManager,
        PasswordManagerModel $passwordManagerModel,
        PasswordManagerService $passwordManagerService,
        JWTService $jWTService,
        AESService $aESService,
    ): stdClass {
        $passwordManager->capsule();

        $data = $jWTService->getTokenData(env('RSA_URL_PATH'));

        $decode = $aESService->decode([
            'idusers' => $data->idusers,
        ]);

        /** @var stdClass $users */
        $users = $passwordManagerModel->getPasswordDB(
            $passwordManager
                ->setIdusers((int) $decode['idusers'])
        );

        $decodePassword = $aESService->decode([
            'users_password' => $passwordManager->getUsersPassword(),
            'users_password_new' => $passwordManager->getUsersPasswordNew(),
            'users_password_confirm' => $passwordManager->getUsersPasswordConfirm(),
        ]);

        $passwordManagerService->verifyPasswords(
            $users->users_password,
            $decodePassword['users_password']
        );

        $passwordManagerService->comparePasswords(
            $passwordManager
                ->setUsersPasswordNew($decodePassword['users_password_new'])
                ->getUsersPasswordNew(),
            $passwordManager
                ->setUsersPasswordConfirm($decodePassword['users_password_confirm'])
                ->getUsersPasswordConfirm()
        );

        $passwordManagerService->updatePassword($passwordManagerModel, $passwordManager);

        return success('password updated successfully');
    }
}
