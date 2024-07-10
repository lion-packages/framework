<?php

declare(strict_types=1);

namespace Tests\App\Models\LionDatabase\MySQL;

use App\Models\LionDatabase\MySQL\AuthenticatorModel;
use App\Models\LionDatabase\MySQL\UsersModel;
use Database\Class\Authenticator2FA;
use Database\Class\LionDatabase\MySQL\Users;
use Database\Factory\LionDatabase\MySQL\UsersFactory;
use Lion\Database\Drivers\Schema\MySQL as Schema;
use Lion\Request\Http;
use Lion\Request\Status;
use Lion\Test\Test;
use PHPUnit\Framework\Attributes\Test as Testing;
use Tests\Providers\AuthJwtProviderTrait;
use Tests\Providers\SetUpMigrationsAndQueuesProviderTrait;

class AuthenticatorModelTest extends Test
{
    use AuthJwtProviderTrait;
    use SetUpMigrationsAndQueuesProviderTrait;

    private AuthenticatorModel $authenticatorModel;
    private UsersModel $usersModel;

    protected function setUp(): void
    {
        $this->runMigrationsAndQueues();

        $this->authenticatorModel = new AuthenticatorModel();

        $this->usersModel = new UsersModel();
    }

    protected function tearDown(): void
    {
        Schema::truncateTable('users')->execute();
    }

    #[Testing]
    public function readUsersPasswordDB(): void
    {
        $users = $this->usersModel->readUsersDB();

        $this->assertIsArray($users);
        $this->assertCount(self::AVAILABLE_USERS, $users);

        $user = reset($users);

        $this->assertIsObject($user);
        $this->assertObjectHasProperty('idusers', $user);

        $capsule = (new Users())
            ->setIdusers($user->idusers)
            ->setUsersPassword(UsersFactory::USERS_PASSWORD);

        /** @var Users $databaseCapsule */
        $databaseCapsule = $this->authenticatorModel->readUsersPasswordDB($capsule);

        $this->assertIsObject($databaseCapsule);
        $this->assertInstances($databaseCapsule, [Users::class]);
        $this->assertIsString($databaseCapsule->getUsersPassword());
    }

    #[Testing]
    public function readCheckStatus(): void
    {
        $users = $this->usersModel->readUsersDB();

        $this->assertIsArray($users);
        $this->assertCount(self::AVAILABLE_USERS, $users);

        $user = reset($users);

        $this->assertIsObject($user);
        $this->assertObjectHasProperty('idusers', $user);

        $status = $this->authenticatorModel->readCheckStatusDB(
            (new Authenticator2FA())
                ->setIdusers($user->idusers)
        );

        $this->assertIsObject($status);
        $this->assertObjectHasProperty('users_2fa', $status);
        $this->assertSame(UsersFactory::DISABLED_2FA, $status->users_2fa);
    }

    #[Testing]
    public function update2FADB(): void
    {
        $users = $this->usersModel->readUsersDB();

        $this->assertIsArray($users);
        $this->assertCount(self::AVAILABLE_USERS, $users);

        $user = reset($users);

        $this->assertIsObject($user);
        $this->assertObjectHasProperty('idusers', $user);

        $capsule = (new Authenticator2FA())
            ->setIdusers($user->idusers)
            ->setUsers2fa(UsersFactory::ENABLED_2FA);

        $response = $this->authenticatorModel->update2FADB($capsule);

        $this->assertIsObject($response);
        $this->assertObjectHasProperty('code', $response);
        $this->assertObjectHasProperty('status', $response);
        $this->assertObjectHasProperty('message', $response);
        $this->assertSame(Http::OK, $response->code);
        $this->assertSame(Status::SUCCESS, $response->status);
        $this->assertSame('Execution finished', $response->message);

        $response = $this->usersModel->readUsersByIdDB(
            (new Users())
                ->setIdusers($capsule->getIdusers())
        );

        $this->assertIsObject($response);
        $this->assertObjectHasProperty('users_2fa', $response);
        $this->assertSame($capsule->getUsers2fa(), $response->users_2fa);
    }
}
