<?php

declare(strict_types=1);

namespace App\Http\Controllers\LionDatabase\MySQL;

use App\Models\LionDatabase\MySQL\LoginModel;
use Database\Class\LionDatabase\MySQL\Users;

/**
 * Controller for user authentication
 *
 * @package App\Http\Controllers\LionDatabase\MySQL
 */
class LoginController
{
    /**
     * Authentic users
     *
     * @param  Users $users [Object of the Users entity]
     * @param  LoginModel $loginModel [Login model object]
     *
     * @return object
     */
	public function auth(Users $users, LoginModel $loginModel): object
	{
		$auth = $loginModel->authDB($users->capsule());

        if ($auth->count === 0) {
            return error('Email/password is incorrect [AUTH-1]');
        }

        $session = $loginModel->sessionDB($users);

        if (!password_verify($users->getUsersPassword(), $session->getUsersPassword())) {
            return error('Email/password is incorrect [AUTH-2]');
        }

        return success('Successfully authenticated user');
	}
}
