<?php

declare(strict_types=1);

namespace App\Http\Controllers\LionDatabase\MySQL;

use App\Models\LionDatabase\MySQL\UsersModel;
use Database\Class\LionDatabase\MySQL\Users;
use Lion\Security\Validation;

class UsersController
{
	public function createUsers(Users $users, UsersModel $usersModel, Validation $validation): object
	{
        $users = $users->capsule();
        $password = $validation->passwordHash($validation->sha256($users->getUsersPassword()));

		return $usersModel->createUsersDB($users->setUsersPassword($password));
	}

	public function readUsers(UsersModel $usersModel): array|object
	{
		return $usersModel->readUsersDB();
	}

	public function updateUsers(Users $users, UsersModel $usersModel, string $idusers): object
	{
		return $usersModel->updateUsersDB($users->capsule()->setIdusers((int) $idusers));
	}

	public function deleteUsers(Users $users, UsersModel $usersModel, string $idusers): object
	{
		return $usersModel->deleteUsersDB($users->capsule()->setIdusers((int) $idusers));
	}
}
