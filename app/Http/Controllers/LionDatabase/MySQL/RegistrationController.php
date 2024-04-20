<?php

declare(strict_types=1);

namespace App\Http\Controllers\LionDatabase\MySQL;

use App\Http\Services\LionDatabase\MySQL\RegistrationService;
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
     * @param Users $users [Capsule for the 'Users' entity]
     * @param UsersModel $usersModel [Model for the Users entity]
     * @param RegistrationService $registrationService [Service that assists the
     * user registration process]
     * @param Validation $validation [Allows you to validate form data and
     * generate encryption safely]
     *
     * @return object
     */
    public function register(
        Users $users,
        UsersModel $usersModel,
        RegistrationService $registrationService,
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
            $registrationService->sendVerifiyEmail($users);
        }

        return $response;
    }
}
