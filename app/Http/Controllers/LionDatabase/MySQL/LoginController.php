<?php

declare(strict_types=1);

namespace App\Http\Controllers\LionDatabase\MySQL;

use App\Models\LionDatabase\MySQL\LoginModel;
use Database\Class\LionDatabase\MySQL\Users;
use Lion\Request\Request;
use Lion\Security\JWT;
use Lion\Security\RSA;

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
     * @param Users $users [Capsule for the 'Users' entity]
     * @param LoginModel $loginModel [Model for user authentication]
     * @param RSA $rsa [Allows you to generate the required configuration for
     * public and private keys, has methods that allow you to encrypt and
     * decrypt data with RSA]
     * @param JWT $jwt [Allows you to generate the required configuration for
     * JWT tokens, has methods that allow you to encrypt and decrypt data with
     * JWT]
     *
     * @return object
     */
    public function auth(Users $users, LoginModel $loginModel, RSA $rsa, JWT $jwt): object
    {
        $auth = $loginModel->authDB($users->capsule());

        if ($auth->count === 0) {
            return error('Email/password is incorrect [AUTH-1]');
        }

        $session = $loginModel->sessionDB($users);

        if (!password_verify($users->getUsersPassword(), $session->getUsersPassword())) {
            return error('Email/password is incorrect [AUTH-2]');
        }

        return success('Successfully authenticated user', Request::HTTP_OK, [
            'jwt' => $jwt
                ->config([
                    'privateKey' => $rsa->setUrlPath(storage_path('keys/'))->init()->getPrivateKey()
                ])
                ->encode([
                    'session' => true,
                    'idusers' => $session->getIdusers(),
                    'idroles' => $session->getIdroles(),
                    'full_name' => "{$session->getUsersName()} {$session->getUsersLastName()}"
                ], (int) env('JWT_EXP', 3600))
                ->get()
        ]);
    }
}
