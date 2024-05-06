<?php

declare(strict_types=1);

namespace Tests\App\Http\Services\LionDatabase\MySQL;

use App\Exceptions\AuthenticationException;
use App\Http\Services\LionDatabase\MySQL\LoginService;
use Database\Class\LionDatabase\MySQL\Users;
use Lion\Dependency\Injection\Container;
use Lion\Test\Test;

class LoginServiceTest extends Test
{
    private LoginService $loginService;

    protected function setUp(): void
    {
        $this->loginService = (new Container())->injectDependencies(new LoginService());
    }

    public function testValidateSession(): void
    {
        $this->expectException(AuthenticationException::class);

        $this->loginService->validateSession(
            (new Users())
                ->setUsersEmail(fake()->email())
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
