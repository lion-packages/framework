<?php

declare(strict_types=1);

namespace App\Http\Controllers\LionDatabase\MySQL;

use App\Http\Services\LionDatabase\MySQL\AccountService;
use App\Http\Services\LionDatabase\MySQL\RegistrationService;
use App\Models\LionDatabase\MySQL\RegistrationModel;
use App\Models\LionDatabase\MySQL\UsersModel;
use Database\Class\LionDatabase\MySQL\Users;
use Lion\Security\Validation;

/**
 * Manage user registration on the platform
 *
 * @package App\Http\Controllers\LionDatabase\MySQL
 */
class RegistrationController
{
    /**
     * Register users with their basic data to create a user account
     *
     * @route api/auth/register
     *
     * @param Users $users [Capsule for the 'Users' entity]
     * @param UsersModel $usersModel [Model for the Users entity]
     * @param AccountService $accountService [Manage user account processes]
     * @param Validation $validation [Allows you to validate form data and
     * generate encryption safely]
     *
     * @return object
     */
    public function register(
        Users $users,
        UsersModel $usersModel,
        AccountService $accountService,
        Validation $validation
    ): object {
        $response = $usersModel->createUsersDB(
            $users
                ->setUsersEmail(request->users_email)
                ->setUsersPassword($validation->passwordHash(request->users_password))
                ->setUsersActivationCode(fake()->numerify('######'))
                ->setUsersCode(uniqid('code-'))
        );

        if (isSuccess($response)) {
            $accountService->sendVerifiyCodeEmail($users);
        }

        return success('registered user successfully');
    }

    /**
     * Validate if an account validation code is correct
     *
     * @route api/auth/verify
     *
     * @param Users $users [Capsule for the 'Users' entity]
     * @param RegistrationModel $registrationModel [Validate in the database if
     * the registration and verification are valid]
     * @param RegistrationService $registrationService [Service that assists the
     * user registration process]
     * @param AccountService $accountService [Manage user account processes]
     *
     * @return object
     */
    public function verifyAccount(
        Users $users,
        RegistrationModel $registrationModel,
        RegistrationService $registrationService,
        AccountService $accountService
    ): object {
        $data = $registrationModel->verifyAccountDB(
            $users
                ->setUsersEmail(request->users_email)
                ->setUsersActivationCode(request->users_activation_code)
        );

        $registrationService->verifyAccount($users, $data);

        $accountService->updateActivationCode(
            $users
                ->setUsersActivationCode(null)
                ->setIdusers($data->idusers)
        );

        return success('user account has been successfully verified');
    }
}
