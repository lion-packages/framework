<?php

declare(strict_types=1);

namespace App\Http\Services\LionDatabase\MySQL;

use App\Exceptions\AuthenticationException;
use App\Models\LionDatabase\MySQL\LoginModel;
use Database\Class\LionDatabase\MySQL\Users;
use Lion\Request\Http;
use Lion\Request\Status;
use Lion\Security\JWT;
use Lion\Security\RSA;

/**
 * Allows you to manage the user authentication process
 *
 * @property RSA $rsa [Allows you to generate the required configuration for
 * public and private keys, has methods that allow you to encrypt and decrypt
 * data with RSA]
 * @property JWT $jwt [Allows you to generate the required configuration for JWT
 * tokens, has methods that allow you to encrypt and decrypt data with JWT]
 * @property LoginModel $loginModel [Model for user authentication]
 *
 * @package App\Http\Services\LionDatabase\MySQL
 */
class LoginService
{
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
     * [Model for user authentication]
     *
     * @var LoginModel $loginModel
     */
    private LoginModel $loginModel;

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
     * @required
     */
    public function setLoginModel(LoginModel $loginModel): void
    {
        $this->loginModel = $loginModel;
    }

    /**
     * Validates the user session
     *
     * @param Users $users [Capsule for the 'Users' entity]
     *
     * @return void
     *
     * @throws AuthenticationException [If the authentication fails]
     */
    public function validateSession(Users $users): void
    {
        $auth = $this->loginModel->authDB($users);

        if ($auth->count === 0 || $auth->count === "0") {
            throw new AuthenticationException(
                'email/password is incorrect [AUTH-1]',
                Status::SESSION_ERROR,
                Http::UNAUTHORIZED
            );
        }
    }

    /**
     * Validates if the account is verified
     *
     * @param Users $users [Capsule for the 'Users' entity]
     *
     * @return void
     *
     * @throws AuthenticationException [If the account has not been verified]
     */
    public function verifyAccountActivation(Users $users): void
    {
        $users_activation_code = $this->loginModel->verifyAccountActivationDB($users);

        if ($users_activation_code->users_activation_code != null) {
            throw new AuthenticationException(
                "the user's account has not yet been verified",
                Status::SESSION_ERROR,
                Http::FORBIDDEN
            );
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
                'privateKey' => $this->rsa
                    ->setUrlPath($path)
                    ->init()
                    ->getPrivateKey()
            ])
            ->encode($data, (int) env('JWT_EXP', 3600))
            ->get();
    }
}
