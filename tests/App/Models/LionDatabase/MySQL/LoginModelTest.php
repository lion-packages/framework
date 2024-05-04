<?php

declare(strict_types=1);

namespace Tests\App\Models\LionDatabase\MySQL;

use App\Models\LionDatabase\MySQL\LoginModel;
use Database\Class\LionDatabase\MySQL\Users;
use Lion\Bundle\Interface\CapsuleInterface;
use Lion\Database\Drivers\Schema\MySQL as Schema;
use Lion\Test\Test;
use Tests\Providers\SetUpMigrationsAndQueuesProviderTrait;

class LoginModelTest extends Test
{
    use SetUpMigrationsAndQueuesProviderTrait;

    const USERS_EMAIL = 'root@dev.com';
    const USERS_EMAIL_ERR = 'sleon@dev.com';

    private LoginModel $loginModel;

    protected function setUp(): void
    {
        $this->runMigrationsAndQueues();

        $this->loginModel = new LoginModel();
    }

    protected function tearDown(): void
    {
        Schema::truncateTable('users')->execute();
    }

    public function testAuthDB(): void
    {
        $response = $this->loginModel->authDB(
            (new Users())
                ->setUsersEmail(self::USERS_EMAIL)
        );

        $this->assertIsObject($response);
        $this->assertSame(1, $response->count);
    }

    public function testAuthEmptyDB(): void
    {
        $response = $this->loginModel->authDB(
            (new Users())
                ->setUsersEmail(self::USERS_EMAIL_ERR)
        );

        $this->assertSame(0, $response->count);
    }

    public function testSessionDB(): void
    {
        $users = (new Users())
            ->setUsersEmail(self::USERS_EMAIL);

        $response = $this->loginModel->sessionDB($users);

        $this->assertInstances($response, [Users::class, CapsuleInterface::class]);
        $this->assertSame($users->getUsersEmail(), $response->getUsersEmail());
    }
}
