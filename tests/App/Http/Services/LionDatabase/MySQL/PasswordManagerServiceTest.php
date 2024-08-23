<?php

declare(strict_types=1);

namespace Tests\App\Http\Services\LionDatabase\MySQL;

use App\Exceptions\PasswordException;
use App\Http\Services\LionDatabase\MySQL\PasswordManagerService;
use App\Models\LionDatabase\MySQL\PasswordManagerModel;
use Database\Class\PasswordManager;
use Database\Factory\LionDatabase\MySQL\UsersFactory;
use Lion\Exceptions\Exception;
use Lion\Request\Http;
use Lion\Request\Status;
use Lion\Security\Validation;
use Lion\Test\Test;
use PHPUnit\Framework\Attributes\Test as Testing;
use Tests\Providers\SetUpMigrationsAndQueuesProviderTrait;

class PasswordManagerServiceTest extends Test
{
    use SetUpMigrationsAndQueuesProviderTrait;

    private PasswordManagerService $passwordManagerService;
    private Validation $validation;

    protected function setUp(): void
    {
        $this->runMigrations();

        $this->validation = new Validation();

        $this->passwordManagerService = (new PasswordManagerService())
            ->setValidation($this->validation);
    }

    /**
     * @throws Exception
     */
    #[Testing]
    public function verifyPasswords(): void
    {
        $this
            ->exception(PasswordException::class)
            ->exceptionMessage('password is incorrect [ERR-1]')
            ->exceptionStatus(Status::ERROR)
            ->exceptionCode(Http::UNAUTHORIZED)
            ->expectLionException(function (): void {
                $usersPassword = $this->validation->passwordHash(
                    $this->validation->sha256(UsersFactory::USERS_PASSWORD)
                );

                $passwordEntered = $this->validation->sha256(UsersFactory::USERS_PASSWORD . '-X');

                $this->passwordManagerService->verifyPasswords($usersPassword, $passwordEntered);
            });
    }

    /**
     * @throws Exception
     */
    #[Testing]
    public function comparePasswords(): void
    {
        $this
            ->exception(PasswordException::class)
            ->exceptionMessage('password is incorrect [ERR-2]')
            ->exceptionStatus(Status::ERROR)
            ->exceptionCode(Http::UNAUTHORIZED)
            ->expectLionException(function (): void {
                $usersPassword = $this->validation->sha256(UsersFactory::USERS_PASSWORD);

                $passwordEntered = $this->validation->sha256(UsersFactory::USERS_PASSWORD . '-X');

                $this->passwordManagerService->comparePasswords($usersPassword, $passwordEntered);
            });
    }

    /**
     * @throws Exception
     */
    #[Testing]
    public function updatePassword(): void
    {
        $this
            ->exception(PasswordException::class)
            ->exceptionMessage('password is incorrect [ERR-3]')
            ->exceptionStatus(Status::ERROR)
            ->exceptionCode(Http::UNAUTHORIZED)
            ->expectLionException(function (): void {
                $this->passwordManagerService->updatePassword(
                    new PasswordManagerModel(),
                    (new PasswordManager())
                        ->setIdusers((int) fake()->numerify('###############'))
                        ->setUsersPasswordConfirm(UsersFactory::USERS_PASSWORD)
                );
            });
    }
}
