<?php

declare(strict_types=1);

namespace App\Http\Controllers\LionDatabase\MySQL;

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
     * @param Validation $validation [Allows you to validate form data and
     * generate encryption safely]
     *
     * @return object
     */
    public function register(Users $users, UsersModel $usersModel, Validation $validation): object
    {
        return $usersModel->createUsersDB(
            $users
                ->setUsersEmail(request->users_email)
                ->setUsersPassword($validation->passwordHash(request->users_password))
                ->setUsersCode(uniqid('code-'))
        );
    }
}
