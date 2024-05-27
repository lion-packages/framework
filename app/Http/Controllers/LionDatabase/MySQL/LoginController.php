<?php

declare(strict_types=1);

namespace App\Http\Controllers\LionDatabase\MySQL;

use App\Exceptions\AuthenticationException;
use App\Exceptions\PasswordException;
use App\Http\Services\LionDatabase\MySQL\LoginService;
use App\Http\Services\LionDatabase\MySQL\PasswordManagerService;
use App\Models\LionDatabase\MySQL\LoginModel;
use Database\Class\LionDatabase\MySQL\Users;
use Lion\Request\Http;

/**
 * Controller for user authentication
 *
 * @package App\Http\Controllers\LionDatabase\MySQL
 */
class LoginController
{
    /**
     * Manage user authentication
     *
     * @route /api/auth/login
     *
     * @param Users $users [Capsule for the 'Users' entity]
     * @param LoginModel $loginModel [Model for user authentication]
     * @param LoginService $loginService [Allows you to manage the user
     * authentication process]
     * @param PasswordManagerService $passwordManagerService
     *
     * @return object
     *
     * @throws AuthenticationException
     * @throws PasswordException
     */
    public function auth(
        Users $users,
        LoginModel $loginModel,
        LoginService $loginService,
        PasswordManagerService $passwordManagerService
    ): object {
        $loginService->validateSession($users->capsule());

        $session = $loginModel->sessionDB($users);

        $passwordManagerService->verifyPasswords(
            $session->users_password,
            $users->getUsersPassword(),
            'email/password is incorrect [AUTH-2]'
        );

        $loginService->verifyAccountActivation($users);

        return success('successfully authenticated user', Http::OK, [
            'full_name' => "{$session->users_name} {$session->users_last_name}",
            'jwt' => $loginService->getToken(env('RSA_URL_PATH'), [
                'session' => true,
                'idusers' => $session->idusers,
                'idroles' => $session->idroles,
            ]),
        ]);
    }
}
