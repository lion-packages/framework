<?php

declare(strict_types=1);

namespace Tests\App\Models\LionDatabase\MySQL;

use App\Models\LionDatabase\MySQL\AuthenticatorModel;
use App\Models\LionDatabase\MySQL\UsersModel;
use Database\Class\LionDatabase\MySQL\Users;
use Database\Factory\LionDatabase\MySQL\UsersFactory;
use Lion\Database\Drivers\Schema\MySQL as Schema;
use Lion\Test\Test;
use PHPUnit\Framework\Attributes\Test as Testing;
use Tests\Providers\SetUpMigrationsAndQueuesProviderTrait;

class AuthenticatorModelTest extends Test
{
    use SetUpMigrationsAndQueuesProviderTrait;

    private AuthenticatorModel $authenticatorModel;

    protected function setUp(): void
    {
        $this->runMigrationsAndQueues();

        $this->authenticatorModel = new AuthenticatorModel();
    }

    protected function tearDown(): void
    {
        Schema::truncateTable('users')->execute();
    }

    #[Testing]
    public function readUsersPasswordDB(): void
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

        /** @var USers $databaseCapsule */
        $databaseCapsule = $this->authenticatorModel->readUsersPasswordDB($capsule);

        $this->assertIsObject($databaseCapsule);
        $this->assertInstances($databaseCapsule, [Users::class]);
        $this->assertIsString($databaseCapsule->getUsersPassword());
    }
}
