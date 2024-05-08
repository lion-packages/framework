<?php

declare(strict_types=1);

namespace App\Http\Controllers\LionDatabase\MySQL;

use App\Http\Services\LionDatabase\MySQL\LoginService;
use App\Http\Services\LionDatabase\MySQL\PasswordManagerService;
use App\Models\LionDatabase\MySQL\LoginModel;
use Database\Class\LionDatabase\MySQL\Users;
use Lion\Request\Request;

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
     * @route api/auth/login
     *
     * @param Users $users [Capsule for the 'Users' entity]
     * @param LoginModel $loginModel [Model for user authentication]
     * @param LoginService $loginService [Service 'LoginService']
     * @param PasswordManagerService $passwordManagerService
     *
     * @return object
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
            $session->getUsersPassword(),
            $users->getUsersPassword(),
            'email/password is incorrect [AUTH-2]'
        );

        $loginService->verifyAccountActivation($users);

        return success('successfully authenticated user', Request::HTTP_OK, [
            'full_name' => "{$session->getUsersName()} {$session->getUsersLastName()}",
            'jwt' => $loginService->getToken(storage_path(env('RSA_URL_PATH')), [
                'session' => true,
                'idusers' => $session->getIdusers(),
                'idroles' => $session->getIdroles(),
            ]),
        ]);
    }
}
