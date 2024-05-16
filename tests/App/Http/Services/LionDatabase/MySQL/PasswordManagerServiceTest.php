<?php

declare(strict_types=1);

namespace Tests\App\Http\Services\LionDatabase\MySQL;

use App\Exceptions\PasswordException;
use App\Http\Services\LionDatabase\MySQL\PasswordManagerService;
use Database\Factory\LionDatabase\MySQL\UsersFactory;
use Lion\Dependency\Injection\Container;
use Lion\Request\Http;
use Lion\Request\Status;
use Lion\Security\Validation;
use Lion\Test\Test;
use Tests\Providers\SetUpMigrationsAndQueuesProviderTrait;

class PasswordManagerServiceTest extends Test
{
    use SetUpMigrationsAndQueuesProviderTrait;

    private PasswordManagerService $passwordManagerService;
    private Validation $validation;

    protected function setUp(): void
    {
        $this->runMigrationsAndQueues();

        $this->passwordManagerService = (new Container())->injectDependencies(new PasswordManagerService());

        $this->validation = new Validation();
    }

    public function testVerifyPasswords(): void
    {
        $this
            ->exception(PasswordException::class)
            ->exceptionMessage('password is incorrect [ERR-1]')
            ->exceptionStatus(Status::ERROR)
            ->exceptionCode(Http::HTTP_UNAUTHORIZED)
            ->expectLionException(function (): void {
                $usersPassword = $this->validation->passwordHash(
                    $this->validation->sha256(UsersFactory::USERS_PASSWORD)
                );

                $passwordEntered = $this->validation->sha256(UsersFactory::USERS_PASSWORD . '-X');

                $this->passwordManagerService->verifyPasswords($usersPassword, $passwordEntered);
            });
    }

    public function testComparePasswords(): void
    {
        $this
            ->exception(PasswordException::class)
            ->exceptionMessage('password is incorrect [ERR-2]')
            ->exceptionStatus(Status::ERROR)
            ->exceptionCode(Http::HTTP_UNAUTHORIZED)
            ->expectLionException(function (): void {
                $usersPassword = $this->validation->sha256(UsersFactory::USERS_PASSWORD);

                $passwordEntered = $this->validation->sha256(UsersFactory::USERS_PASSWORD . '-X');

                $this->passwordManagerService->comparePasswords($usersPassword, $passwordEntered);
            });
    }

    public function testUpdatePassword(): void
    {
        $this->expectNotToPerformAssertions();
    }
}
