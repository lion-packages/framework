<?php

declare(strict_types=1);

namespace App\Http\Controllers\LionDatabase\MySQL;

use App\Models\LionDatabase\MySQL\UsersModel;
use Database\Class\LionDatabase\MySQL\Users;
use Lion\Security\Validation;

/**
 * Controller for the Users entity
 *
 * @package App\Http\Controllers\LionDatabase\MySQL
 */
class UsersController
{
    /**
     * Create users
     *
     * @param Users $users [Object of the Users entity]
     * @param UsersModel $usersModel [Model object Users]
     * @param Validation $validation [Object to perform validations]
     *
     * @return object
     */
    public function createUsers(Users $users, UsersModel $usersModel, Validation $validation): object
    {
        return $usersModel->createUsersDB(
            $users
                ->capsule()
                ->setUsersPassword($validation->passwordHash($users->getUsersPassword()))
                ->setUsersCode(uniqid('code-'))
        );
    }

    /**
     * Read users
     *
     * @param UsersModel $usersModel [Model object Users]
     *
     * @return array|object
     */
    public function readUsers(UsersModel $usersModel): array|object
    {
        return $usersModel->readUsersDB();
    }

    /**
     * Update users
     *
     * @param Users $users [Object of the Users entity]
     * @param UsersModel $usersModel [Model object Users]
     * @param string $idusers [user id defined in routes]
     *
     * @return object
     */
    public function updateUsers(Users $users, UsersModel $usersModel, string $idusers): object
    {
        return $usersModel->updateUsersDB(
            $users
                ->capsule()
                ->setIdusers((int) $idusers)
        );
    }

    /**
     * Delete users
     *
     * @param Users $users [Object of the Users entity]
     * @param UsersModel $usersModel [Model object Users]
     * @param string $idusers [user id defined in routes]
     *
     * @return object
     */
    public function deleteUsers(Users $users, UsersModel $usersModel, string $idusers): object
    {
        return $usersModel->deleteUsersDB(
            $users
                ->capsule()
                ->setIdusers((int) $idusers)
        );
    }
}
