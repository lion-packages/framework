<?php

declare(strict_types=1);

namespace App\Http\Services\LionDatabase\MySQL;

use App\Exceptions\AuthenticationException;
use App\Http\Services\AESService;
use App\Http\Services\JWTService;
use App\Models\LionDatabase\MySQL\AuthenticatorModel;
use App\Models\LionDatabase\MySQL\LoginModel;
use Database\Class\Authenticator2FA;
use Database\Class\LionDatabase\MySQL\Users;
use Database\Factory\LionDatabase\MySQL\UsersFactory;
use Lion\Request\Http;
use Lion\Request\Status;
use Lion\Security\JWT;
use Lion\Security\RSA;
use stdClass;

/**
 * Allows you to manage the user authentication process
 *
 * @property RSA $rsa [Allows you to generate the required configuration for
 * public and private keys, has methods that allow you to encrypt and decrypt
 * data with RSA]
 * @property JWT $jwt [Allows you to generate the required configuration for JWT
 * tokens, has methods that allow you to encrypt and decrypt data with JWT]
 * @property LoginModel $loginModel [Model for user authentication]
 * @property AuthenticatorModel $authenticatorModel [Perform queries to validate
 * user authentication through 2FA]
 * @property AESService $aESService [Encrypt and decrypt data with AES]
 * @property JWTService $jWTService [Service to manipulate JWT tokens]
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
     * [Perform queries to validate user authentication through 2FA]
     *
     * @var AuthenticatorModel $authenticatorModel
     */
    private AuthenticatorModel $authenticatorModel;

    /**
     * [Encrypt and decrypt data with AES]
     *
     * @var AESService $aESService
     */
    private AESService $aESService;

    /**
     * [Service to manipulate JWT tokens]
     *
     * @var JWTService $jWTService
     */
    private JWTService $jWTService;

    /**
     * @required
     */
    public function setRSA(RSA $rsa): LoginService
    {
        $this->rsa = $rsa;

        return $this;
    }

    /**
     * @required
     */
    public function setJWT(JWT $jwt): LoginService
    {
        $this->jwt = $jwt;

        return $this;
    }

    /**
     * @required
     */
    public function setLoginModel(LoginModel $loginModel): LoginService
    {
        $this->loginModel = $loginModel;

        return $this;
    }

    /**
     * @required
     */
    public function setAuthenticatorModel(AuthenticatorModel $authenticatorModel): LoginService
    {
        $this->authenticatorModel = $authenticatorModel;

        return $this;
    }

    /**
     * @required
     */
    public function setAESService(AESService $aESService): LoginService
    {
        $this->aESService = $aESService;

        return $this;
    }

    /**
     * @required
     */
    public function setJWTService(JWTService $jWTService): LoginService
    {
        $this->jWTService = $jWTService;

        return $this;
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
        /** @var stdClass $users_activation_code */
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
     * @param string|int $time [Token useful life]
     * @param array<string, mixed> $data [Data that is added to the JWT token]
     *
     * @return string
     */
    public function getToken(string|int $time, array $data): string
    {
        return $this->jwt
            ->config([
                'privateKey' => $this->rsa
                    ->setUrlPath(storage_path(env('RSA_URL_PATH')))
                    ->init()
                    ->getPrivateKey()
            ])
            ->encode($data, is_string($time) ? (int) $time : $time)
            ->get();
    }

    /**
     * Generate authentication tokens and to refresh sessions
     *
     * @param Users $users [Capsule for the 'Users' entity]
     *
     * @return array<string, string>
     */
    public function generateTokens(Users $users): array
    {
        $encode = $this->aESService->encode([
            'idusers' => (string) $users->getIdusers(),
            'idroles' => (string) $users->getIdroles(),
        ]);

        $encodeToken = $this->aESService->encode([
            'jwt_refresh' => $this->getToken(env('JWT_REFRESH_EXP'), [
                'session' => true,
                'idusers' => $encode['idusers'],
                'idroles' => $encode['idroles'],
            ]),
        ]);

        return [
            'jwt_access' => $this->getToken(env('JWT_EXP'), [
                'session' => true,
                'idusers' => $encode['idusers'],
                'idroles' => $encode['idroles'],
            ]),
            'jwt_refresh' => $encodeToken['jwt_refresh'],
        ];
    }

    /**
     * Validate if the refresh token is still valid
     *
     * @param string $jwt [Token to decrypt]
     *
     * @return void
     *
     * @throws AuthenticationException [If the token is not valid]
     */
    public function validateRefreshToken(string $jwt): void
    {
        $decode = $this->jWTService->decode(env('RSA_URL_PATH'), $jwt);

        if (isError($decode)) {
            throw new AuthenticationException('user not logged in, you must log in', Status::ERROR, Http::UNAUTHORIZED);
        };
    }

    /**
     * Validates if the user has 2FA security activated
     *
     * @param Authenticator2FA $authenticator2FA [Capsule for the
     * 'Authenticator2FA' entity]
     *
     * @return bool
     */
    public function checkStatus2FA(Authenticator2FA $authenticator2FA): bool
    {
        /** @var stdClass $status */
        $status = $this->authenticatorModel->readCheckStatusDB($authenticator2FA);

        return UsersFactory::ENABLED_2FA === $status->users_2fa;
    }
}
