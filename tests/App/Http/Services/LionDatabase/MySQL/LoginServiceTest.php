<?php

declare(strict_types=1);

namespace Tests\App\Http\Services\LionDatabase\MySQL;

use App\Enums\RolesEnum;
use App\Exceptions\AuthenticationException;
use App\Http\Services\AESService;
use App\Http\Services\JWTService;
use App\Http\Services\LionDatabase\MySQL\LoginService;
use App\Models\LionDatabase\MySQL\AuthenticatorModel;
use App\Models\LionDatabase\MySQL\LoginModel;
use App\Models\LionDatabase\MySQL\UsersModel;
use Database\Class\Authenticator2FA;
use Database\Class\LionDatabase\MySQL\Users;
use Database\Migrations\LionDatabase\MySQL\Tables\DocumentTypes as DocumentTypesTable;
use Database\Migrations\LionDatabase\MySQL\Tables\Roles as RolesTable;
use Database\Migrations\LionDatabase\MySQL\Tables\Users as UsersTable;
use Database\Migrations\LionDatabase\MySQL\Views\ReadUsersById;
use Database\Seed\LionDatabase\MySQL\DocumentTypesSeed;
use Database\Seed\LionDatabase\MySQL\RolesSeed;
use Database\Seed\LionDatabase\MySQL\UsersSeed;
use Lion\Bundle\Test\Test;
use Lion\Exceptions\Exception;
use Lion\Request\Http;
use Lion\Request\Status;
use Lion\Security\AES;
use Lion\Security\Exceptions\AESException;
use Lion\Security\JWT;
use Lion\Security\RSA;
use PHPUnit\Framework\Attributes\Test as Testing;
use ReflectionException;
use stdClass;
use Tests\Providers\AuthJwtProviderTrait;

class LoginServiceTest extends Test
{
    use AuthJwtProviderTrait;

    private const string USERS_EMAIL = 'manager@dev.com';

    private LoginService $loginService;
    private UsersModel $usersModel;

    /**
     * @throws ReflectionException
     */
    protected function setUp(): void
    {
        $this->executeMigrationsGroup([
            DocumentTypesTable::class,
            RolesTable::class,
            UsersTable::class,
            ReadUsersById::class,
        ]);

        $this->executeSeedsGroup([
            DocumentTypesSeed::class,
            RolesSeed::class,
            UsersSeed::class,
        ]);

        $this->loginService = (new LoginService())
            ->setRSA(new RSA())
            ->setJWT(new JWT())
            ->setLoginModel(new LoginModel())
            ->setAuthenticatorModel(new AuthenticatorModel())
            ->setAESService(
                (new AESService())
                    ->setAES(new AES())
            )
            ->setJWTService(
                (new JWTService())
                    ->setJWT(new JWT())
                    ->setRSA(new RSA())
            );

        $this->usersModel = new UsersModel();

        $this->initReflection($this->loginService);
    }

    /**
     * @throws ReflectionException
     */
    #[Testing]
    public function setRSA(): void
    {
        $this->assertInstanceOf(LoginService::class, $this->loginService->setRSA(new RSA()));
        $this->assertInstanceOf(RSA::class, $this->getPrivateProperty('rsa'));
    }

    /**
     * @throws ReflectionException
     */
    #[Testing]
    public function setJWT(): void
    {
        $this->assertInstanceOf(LoginService::class, $this->loginService->setJWT(new JWT()));
        $this->assertInstanceOf(JWT::class, $this->getPrivateProperty('jwt'));
    }

    /**
     * @throws ReflectionException
     */
    #[Testing]
    public function setLoginModel(): void
    {
        $this->assertInstanceOf(LoginService::class, $this->loginService->setLoginModel(new LoginModel()));
        $this->assertInstanceOf(LoginModel::class, $this->getPrivateProperty('loginModel'));
    }

    /**
     * @throws ReflectionException
     */
    #[Testing]
    public function setAuthenticatorModel(): void
    {
        $this->assertInstanceOf(
            LoginService::class,
            $this->loginService->setAuthenticatorModel(new AuthenticatorModel())
        );

        $this->assertInstanceOf(AuthenticatorModel::class, $this->getPrivateProperty('authenticatorModel'));
    }

    /**
     * @throws ReflectionException
     */
    #[Testing]
    public function setAESService(): void
    {
        $this->assertInstanceOf(LoginService::class, $this->loginService->setAESService(new AESService()));
        $this->assertInstanceOf(AESService::class, $this->getPrivateProperty('aESService'));
    }

    /**
     * @throws ReflectionException
     */
    #[Testing]
    public function setJWTService(): void
    {
        $this->assertInstanceOf(LoginService::class, $this->loginService->setJWTService(new JWTService()));
        $this->assertInstanceOf(JWTService::class, $this->getPrivateProperty('jWTService'));
    }

    /**
     * @throws Exception
     */
    #[Testing]
    public function validateSession(): void
    {
        $this
            ->exception(AuthenticationException::class)
            ->exceptionMessage('email/password is incorrect [AUTH-1]')
            ->exceptionStatus(Status::SESSION_ERROR)
            ->exceptionCode(Http::UNAUTHORIZED)
            ->expectLionException(function (): void {
                $this->loginService->validateSession(
                    (new Users())
                        ->setUsersEmail(fake()->email())
                );
            });
    }

    /**
     * @throws Exception
     */
    #[Testing]
    public function verifyAccountActivation(): void
    {
        $this
            ->exception(AuthenticationException::class)
            ->exceptionMessage("the user's account has not yet been verified")
            ->exceptionStatus(Status::SESSION_ERROR)
            ->exceptionCode(Http::FORBIDDEN)
            ->expectLionException(function (): void {
                $this->loginService->verifyAccountActivation(
                    (new Users())
                        ->setUsersEmail(self::USERS_EMAIL)
                );
            });
    }

    #[Testing]
    public function getToken(): void
    {
        $token = $this->loginService->getToken(env('JWT_EXP'), [
            'session' => true,
        ]);

        $this->assertIsString($token);
    }

    /**
     * @throws AESException
     */
    #[Testing]
    public function generateTokens(): void
    {
        $users = (new Users())
            ->setIdusers(1)
            ->setIdroles(RolesEnum::ADMINISTRATOR->value);

        $tokens = $this->loginService->generateTokens($users);

        $this->assertIsArray($tokens);
        $this->assertArrayHasKey('jwt_access', $tokens);
        $this->assertArrayHasKey('jwt_refresh', $tokens);
        $this->assertIsString($tokens['jwt_access']);
        $this->assertIsString($tokens['jwt_refresh']);
    }

    /**
     * @throws AuthenticationException
     * @throws Exception
     */
    #[Testing]
    public function validateRefreshToken(): void
    {
        $this->exception(AuthenticationException::class)
            ->exceptionMessage('user not logged in, you must log in')
            ->exceptionCode(Http::UNAUTHORIZED)
            ->exceptionStatus(Status::ERROR)
            ->expectLionException(function (): void {
                $this->loginService->validateRefreshToken(uniqid());
            });
    }

    #[Testing]
    public function checkStatus2FA(): void
    {
        $users = $this->usersModel->readUsersDB();

        $this->assertIsArray($users);
        $this->assertCount(self::AVAILABLE_USERS, $users);

        $user = reset($users);

        $this->assertIsObject($user);
        $this->assertInstanceOf(stdClass::class, $user);
        $this->assertObjectHasProperty('idusers', $user);
        $this->assertIsInt($user->idusers);

        $response = $this->loginService
            ->checkStatus2FA(
                (new Authenticator2FA())->setIdusers($user->idusers)
            );

        $this->assertIsBool($response);
        $this->assertFalse($response);
    }
}
