<?php

declare(strict_types=1);

namespace App\Models\LionDatabase\MySQL;

use Database\Class\LionDatabase\MySQL\Users;
use Lion\Database\Drivers\MySQL as DB;

class UsersModel
{
	public function createUsersDB(Users $users): object
	{
		return DB::call('create_users', [
			$users->getIdroles(),
            $users->getIddocumentTypes(),
			$users->getUsersName(),
			$users->getUsersLastName(),
			$users->getUsersEmail(),
			$users->getUsersPassword(),
			$users->getUsersCode(),
		])->execute();
	}

	public function readUsersDB(): array|object
	{
		return DB::view('read_users')->select()->getAll();
	}

	public function updateUsersDB(Users $users): object
	{
		return DB::call('update_users', [
			$users->getIdroles(),
            $users->getIddocumentTypes(),
			$users->getUsersName(),
			$users->getUsersLastName(),
			$users->getUsersEmail(),
			$users->getIdusers(),
		])->execute();
	}

	public function deleteUsersDB(Users $users): object
	{
		return DB::call('delete_users', [
			$users->getIdusers(),
		])->execute();
	}
}
