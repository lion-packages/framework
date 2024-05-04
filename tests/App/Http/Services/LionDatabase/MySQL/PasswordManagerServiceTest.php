<?php

declare(strict_types=1);

namespace Tests\App\Http\Services\LionDatabase\MySQL;

use App\Exceptions\PasswordException;
use App\Http\Services\LionDatabase\MySQL\PasswordManagerService;
use Database\Factory\LionDatabase\MySQL\UsersFactory;
use Lion\Request\Request;
use Lion\Security\Validation;
use Lion\Test\Test;

class PasswordManagerServiceTest extends Test
{
    private PasswordManagerService $passwordManagerService;
    private Validation $validation;

    protected function setUp(): void
    {
        $this->passwordManagerService = new PasswordManagerService();

        $this->validation = new Validation();
    }

    public function testVerifyPasswords(): void
    {
        $this->expectException(PasswordException::class);
        $this->expectExceptionCode(Request::HTTP_UNAUTHORIZED);
        $this->expectExceptionMessage('password is incorrect [ERR-1]');

        $usersPassword = $this->validation->passwordHash($this->validation->sha256(UsersFactory::USERS_PASSWORD));

        $passwordEntered = $this->validation->sha256(UsersFactory::USERS_PASSWORD . '-X');

        $this->passwordManagerService->verifyPasswords($usersPassword, $passwordEntered);
    }

    public function testComparePasswords(): void
    {
        $this->expectException(PasswordException::class);
        $this->expectExceptionCode(Request::HTTP_UNAUTHORIZED);
        $this->expectExceptionMessage('password is incorrect [ERR-2]');

        $usersPassword = $this->validation->sha256(UsersFactory::USERS_PASSWORD);

        $passwordEntered = $this->validation->sha256(UsersFactory::USERS_PASSWORD . '-X');

        $this->passwordManagerService->comparePasswords($usersPassword, $passwordEntered);
    }
}
