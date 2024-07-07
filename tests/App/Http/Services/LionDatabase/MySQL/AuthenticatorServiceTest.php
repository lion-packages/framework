<?php

declare(strict_types=1);

namespace Tests\App\Http\Services\LionDatabase\MySQL;

use App\Exceptions\PasswordException;
use App\Http\Services\LionDatabase\MySQL\AuthenticatorService;
use App\Models\LionDatabase\MySQL\AuthenticatorModel;
use App\Models\LionDatabase\MySQL\UsersModel;
use Database\Class\LionDatabase\MySQL\Users;
use Database\Factory\LionDatabase\MySQL\UsersFactory;
use Lion\Database\Drivers\Schema\MySQL as Schema;
use Lion\Request\Http;
use Lion\Request\Status;
use Lion\Test\Test;
use PHPUnit\Framework\Attributes\Test as Testing;
use Tests\Providers\SetUpMigrationsAndQueuesProviderTrait;

class AuthenticatorServiceTest extends Test
{
    use SetUpMigrationsAndQueuesProviderTrait;

    private AuthenticatorService $authenticatorService;

    protected function setUp(): void
    {
        $this->runMigrationsAndQueues();

        $this->authenticatorService = new AuthenticatorService();
    }

    protected function tearDown(): void
    {
        Schema::truncateTable('users')->execute();
    }

    #[Testing]
    public function setAuthenticatorModel(): void
    {
        $this->initReflection($this->authenticatorService);

        $this->assertInstanceOf(
            AuthenticatorService::class,
            $this->authenticatorService->setAuthenticatorModel(new AuthenticatorModel())
        );

        $this->assertInstanceOf(AuthenticatorModel::class, $this->getPrivateProperty('authenticatorModel'));
    }

    #[Testing]
    public function passwordVerify(): void
    {
        $users = (new UsersModel())->readUsersDB();

        $this->assertIsArray($users);
        $this->assertCount(2, $users);

        $user = reset($users);

        $this->assertIsObject($user);
        $this->assertObjectHasProperty('idusers', $user);

        $capsule = (new Users())
            ->setIdusers($user->idusers)
            ->setUsersPassword(UsersFactory::USERS_PASSWORD);

        $this->authenticatorService
            ->setAuthenticatorModel(new AuthenticatorModel())
            ->passwordVerify($capsule);
    }

    #[Testing]
    public function passwordVerifyPasswordIsInvalid(): void
    {
        $this
            ->exception(PasswordException::class)
            ->exceptionMessage('password is invalid')
            ->exceptionStatus(Status::ERROR)
            ->exceptionCode(Http::INTERNAL_SERVER_ERROR)
            ->expectLionException(function (): void {
                $users = (new UsersModel())->readUsersDB();

                $this->assertIsArray($users);
                $this->assertCount(2, $users);

                $user = reset($users);

                $this->assertIsObject($user);
                $this->assertObjectHasProperty('idusers', $user);

                $capsule = (new Users())
                    ->setIdusers($user->idusers)
                    ->setUsersPassword(fake()->numerify('#########'));

                $this->authenticatorService
                    ->setAuthenticatorModel(new AuthenticatorModel())
                    ->passwordVerify($capsule);
            });
    }
}
