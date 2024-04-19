<?php

declare(strict_types=1);

namespace Tests\App\Http\Services\LionDatabase\MySQL;

use App\Exceptions\AuthenticationException;
use App\Http\Services\LionDatabase\MySQL\LoginService;
use App\Models\LionDatabase\MySQL\LoginModel;
use Database\Class\LionDatabase\MySQL\Users;
use Lion\Dependency\Injection\Container;
use Lion\Security\Validation;
use Lion\Test\Test;

class LoginServiceTest extends Test
{
    private LoginService $loginService;
    private Validation $validation;

    protected function setUp(): void
    {
        $this->loginService = (new Container())->injectDependencies(new LoginService());

        $this->validation = new Validation();
    }

    public function testValidateSession(): void
    {
        $this->expectException(AuthenticationException::class);

        $this->loginService->validateSession(
            new LoginModel(),
            (new Users())->setUsersEmail(fake()->email())
        );
    }

    public function testPasswordVerify(): void
    {
        $this->expectException(AuthenticationException::class);

        $this->loginService->passwordVerify(
            $this->validation->sha256('lion'),
            $this->validation->passwordHash($this->validation->sha256('test'))
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
