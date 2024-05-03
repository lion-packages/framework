<?php

declare(strict_types=1);

namespace App\Http\Controllers\LionDatabase\MySQL;

use App\Http\Services\JWTService;
use App\Http\Services\LionDatabase\MySQL\PasswordManagerService;
use App\Models\LionDatabase\MySQL\PasswordManagerModel;
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
     * Manage system password recovery
     *
     * @param PasswordManager $passwordManager [Capsule for the
     * 'PasswordManager' entity]
     * @param PasswordManagerModel $passwordManagerModel [Password management
     * model]
     * @param PasswordManagerService $passwordManagerService [Manage different
     * processes for strong password verifications]
     * @param JWTService $jWTService [Service to manipulate JWT tokens]
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

        $users = $passwordManagerModel->getPasswordDB($passwordManager->capsule()->setIdusers($data->idusers));

        $passwordManagerService->verifyPasswords(
            $users->users_password,
            $passwordManager->getUsersPassword(),
            "the user's password is not valid [ERR-1]"
        );

        $passwordManagerService->comparePasswords(
            $passwordManager->getUsersPasswordNew(),
            $passwordManager->getUsersPasswordConfirm(),
            "the user's password is not valid [ERR-2]"
        );

        $passwordManagerModel->updatePasswordDB(
            $passwordManager
                ->setUsersPasswordConfirm($validation->passwordHash($passwordManager->getUsersPasswordConfirm()))
        );

        return success('password updated successfully');
    }
}
