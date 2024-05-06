<?php

declare(strict_types=1);

namespace Tests\App\Http\Services\LionDatabase\MySQL;

use App\Exceptions\AuthenticationException;
use App\Http\Services\LionDatabase\MySQL\LoginService;
use Database\Class\LionDatabase\MySQL\Users;
use Lion\Dependency\Injection\Container;
use Lion\Request\Request;
use Lion\Test\Test;
use Tests\Providers\SetUpMigrationsAndQueuesProviderTrait;

class LoginServiceTest extends Test
{
    use SetUpMigrationsAndQueuesProviderTrait;

    const USERS_EMAIL = 'manager@dev.com';

    private LoginService $loginService;

    protected function setUp(): void
    {
        $this->runMigrationsAndQueues();

        $this->loginService = (new Container())
            ->injectDependencies(new LoginService());
    }

    public function testValidateSession(): void
    {
        $this->expectException(AuthenticationException::class);

        $this->loginService->validateSession(
            (new Users())
                ->setUsersEmail(fake()->email())
        );
    }

    public function testVerifyAccountActivation(): void
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionCode(Request::HTTP_FORBIDDEN);
        $this->expectExceptionMessage("the user's account has not yet been verified");

        $this->loginService->verifyAccountActivation(
            (new Users())
                ->setUsersEmail(self::USERS_EMAIL)
        );
    }

    public function testGetToken(): void
    {
        $token = $this->loginService->getToken(storage_path(env('RSA_URL_PATH'), false), [
            'session' => true
        ]);

        $this->assertIsString($token);
    }
}
