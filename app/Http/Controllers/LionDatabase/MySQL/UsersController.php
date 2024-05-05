<?php

declare(strict_types=1);

namespace App\Http\Controllers\LionDatabase\MySQL;

use App\Models\LionDatabase\MySQL\UsersModel;
use Database\Class\LionDatabase\MySQL\Users;
use Exception;
use Lion\Request\Request;
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
     * @param Users $users [Capsule for the 'Users' entity]
     * @param UsersModel $usersModel [Model for the Users entity]
     * @param Validation $validation [Allows you to validate form data and
     * generate encryption safely]
     *
     * @return object
     */
    public function createUsers(Users $users, UsersModel $usersModel, Validation $validation): object
    {
        $response = $usersModel->createUsersDB(
            $users
                ->capsule()
                ->setUsersPassword($validation->passwordHash($users->getUsersPassword()))
                ->setUsersActivationCode(fake()->numerify('######'))
                ->setUsersRecoveryCode(null)
                ->setUsersCode(uniqid('code-'))
        );

        if (isError($response)) {
            throw new Exception('an error occurred while registering the user', Request::HTTP_INTERNAL_SERVER_ERROR);
        }

        return success('registered user successfully');
    }

    /**
     * Read users
     *
     * @param UsersModel $usersModel [Model for the Users entity]
     *
     * @return array|object
     */
    public function readUsers(UsersModel $usersModel): array|object
    {
        $data = $usersModel->readUsersDB();

        if (isSuccess($data)) {
            return success($data->message);
        }

        return $data;
    }

    /**
     * Read users by id
     *
     * @param Users $users [Capsule for the 'Users' entity]
     * @param UsersModel $usersModel [Model for the Users entity]
     * @param string $idusers [user id defined in routes]
     *
     * @return array|object
     */
    public function readUsersById(Users $users, UsersModel $usersModel, string $idusers): array|object
    {
        $data = $usersModel->readUsersByIdDB(
            $users
                ->setIdusers((int) $idusers)
        );

        if (isSuccess($data)) {
            return success($data->message);
        }

        return $data;
    }

    /**
     * Update users
     *
     * @param Users $users [Capsule for the 'Users' entity]
     * @param UsersModel $usersModel [Model for the Users entity]
     * @param string $idusers [user id defined in routes]
     *
     * @return object
     */
    public function updateUsers(Users $users, UsersModel $usersModel, string $idusers): object
    {
        $response = $usersModel->updateUsersDB(
            $users
                ->capsule()
                ->setIdusers((int) $idusers)
        );

        if (isError($response)) {
            throw new Exception('an error occurred while updating the user', Request::HTTP_INTERNAL_SERVER_ERROR);
        }

        return success('the registered user has been successfully updated');
    }

    /**
     * Delete users
     *
     * @param Users $users [Capsule for the 'Users' entity]
     * @param UsersModel $usersModel [Model for the Users entity]
     * @param string $idusers [user id defined in routes]
     *
     * @return object
     */
    public function deleteUsers(Users $users, UsersModel $usersModel, string $idusers): object
    {
        $response = $usersModel->deleteUsersDB(
            $users
                ->setIdusers((int) $idusers)
        );

        if (isError($response)) {
            throw new Exception('an error occurred while deleting the user', Request::HTTP_INTERNAL_SERVER_ERROR);
        }

        return success('the registered user has been successfully deleted');
    }
}
