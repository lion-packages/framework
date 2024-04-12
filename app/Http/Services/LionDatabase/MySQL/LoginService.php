<?php

declare(strict_types=1);

namespace App\Http\Services\LionDatabase\MySQL;

use App\Exceptions\AuthenticationException;
use App\Models\LionDatabase\MySQL\LoginModel;
use Database\Class\LionDatabase\MySQL\Users;
use Lion\Request\Request;
use Lion\Security\JWT;
use Lion\Security\RSA;

/**
 * Service 'LoginService'
 *
 * @property RSA $rsa [Allows you to generate the required configuration for
 * public and private keys, has methods that allow you to encrypt and decrypt
 * data with RSA]
 * @property JWT $jwt [Allows you to generate the required configuration for JWT
 * tokens, has methods that allow you to encrypt and decrypt data with JWT]
 *
 * @package App\Http\Services\LionDatabase\MySQL
 */
class LoginService
{
    /**
     * [Defines the error state for an unauthorized session]
     *
     * @const AUTH_ERROR
     */
    const AUTH_ERROR = 'auth-error';

    /**
     * [Allows you to generate the required configuration for public and private
     * keys, has methods that allow you to encrypt and decrypt data with RSA]
     *
     * @var RSA $rsa
     */
    private RSA $rsa;

    /**
     * [Allows you to generate the required configuration for JWT tokens, has
     * methods that allow you to encrypt and decrypt data with JWT]
     *
     * @var JWT $jwt
     */
    private JWT $jwt;

    /**
     * @required
     */
    public function setRSA(RSA $rsa): void
    {
        $this->rsa = $rsa;
    }

    /**
     * @required
     */
    public function setJWT(JWT $jwt): void
    {
        $this->jwt = $jwt;
    }

    /**
     * Validates the user session
     *
     * @param LoginModel $loginModel [Model for user authentication]
     * @param Users $users [Capsule for the 'Users' entity]
     *
     * @return void
     *
     * @throws AuthenticationException [If the authentication fails]
     */
    public function validateSession(LoginModel $loginModel, Users $users): void
    {
        $auth = $loginModel->authDB($users);

        if ($auth->count === 0) {
            throw new AuthenticationException('email/password is incorrect [AUTH-1]', Request::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Verifies a password.
     *
     * @param string $usersPassword [The password provided by the user]
     * @param string $sessionPassword [The password stored in the session]
     *
     * @return void
     *
     * @throws AuthenticationException [If the passwords do not match]
     */
    public function passwordVerify(string $usersPassword, string $sessionPassword): void
    {
        if (!password_verify($usersPassword, $sessionPassword)) {
            throw new AuthenticationException('email/password is incorrect [AUTH-2]', Request::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Generate a JWT token for user authorization
     *
     * @param string $path [Path where RSA public and private keys are defined]
     * @param array<string, mixed> $data [Data that is added to the JWT token]
     *
     * @return string
     */
    public function getToken(string $path, array $data): string
    {
        return $this->jwt
            ->config([
                'privateKey' => $this->rsa->setUrlPath($path)->init()->getPrivateKey()
            ])
            ->encode($data, (int) env('JWT_EXP', 3600))
            ->get();
    }
}
