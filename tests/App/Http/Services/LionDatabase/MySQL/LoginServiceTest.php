<?php

declare(strict_types=1);

namespace Tests\App\Http\Services\LionDatabase\MySQL;

use App\Enums\RolesEnum;
use App\Exceptions\AuthenticationException;
use App\Http\Services\LionDatabase\MySQL\LoginService;
use Database\Class\LionDatabase\MySQL\Users;
use Lion\Dependency\Injection\Container;
use Lion\Exceptions\Exception;
use Lion\Request\Http;
use Lion\Request\Status;
use Lion\Test\Test;
use Tests\Providers\AuthJwtProviderTrait;
use Tests\Providers\SetUpMigrationsAndQueuesProviderTrait;

class LoginServiceTest extends Test
{
    use AuthJwtProviderTrait;
    use SetUpMigrationsAndQueuesProviderTrait;

    const string USERS_EMAIL = 'manager@dev.com';

    private LoginService $loginService;

    protected function setUp(): void
    {
        $this->runMigrationsAndQueues();

        $this->loginService = (new Container())
            ->injectDependencies(new LoginService());
    }

    /**
     * @throws Exception
     */
    public function testValidateSession(): void
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
    public function testVerifyAccountActivation(): void
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

    public function testGetToken(): void
    {
        $token = $this->loginService->getToken(env('JWT_EXP'), [
            'session' => true,
        ]);

        $this->assertIsString($token);
    }

    public function testGenerateTokens(): void
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
     * @throws Exception
     */
    public function testValidateRefreshToken(): void
    {
        $this->exception(AuthenticationException::class)
            ->exceptionMessage('user not logged in, you must log in')
            ->exceptionCode(Http::UNAUTHORIZED)
            ->exceptionStatus(Status::ERROR)
            ->expectLionException(function (): void {
                $this->loginService->validateRefreshToken(uniqid());
            });
    }
}
