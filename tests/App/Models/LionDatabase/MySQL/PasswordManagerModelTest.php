<?php

declare(strict_types=1);

namespace Tests\App\Models\LionDatabase\MySQL;

use App\Models\LionDatabase\MySQL\PasswordManagerModel;
use App\Models\LionDatabase\MySQL\UsersModel;
use Database\Class\PasswordManager;
use Database\Factory\LionDatabase\MySQL\UsersFactory;
use Lion\Database\Drivers\Schema\MySQL as Schema;
use Lion\Request\Status;
use Lion\Security\Validation;
use Lion\Test\Test;
use Tests\Providers\SetUpMigrationsAndQueuesProviderTrait;

class PasswordManagerModelTest extends Test
{
    use SetUpMigrationsAndQueuesProviderTrait;

    const USERS_PASSWORD = 'lion-password';

    private PasswordManagerModel $passwordManagerModel;
    private Validation $validation;

    protected function setUp(): void
    {
        $this->runMigrationsAndQueues();

        $this->passwordManagerModel = new PasswordManagerModel();

        $this->validation = new Validation();
    }

    protected function tearDown(): void
    {
        Schema::truncateTable('users')->execute();
    }

    public function testGetPasswordDB(): void
    {
        $users = (new UsersModel())->readUsersDB();

        $this->assertIsArray($users);

        $user = reset($users);

        $this->assertIsObject($user);
        $this->assertObjectHasProperty('idusers', $user);

        $hash = $this->passwordManagerModel->getPasswordDB(
            (new PasswordManager())
                ->setIdusers($user->idusers)
        );

        $this->assertIsObject($hash);
        $this->assertObjectHasProperty('users_password', $hash);

        $password = $this->validation->sha256(UsersFactory::USERS_PASSWORD);

        $this->assertTrue(password_verify($password, $hash->users_password));
    }

    public function testUpdatePasswordDB(): void
    {
        $users = (new UsersModel())->readUsersDB();

        $this->assertIsArray($users);

        $user = reset($users);

        $this->assertIsObject($user);
        $this->assertObjectHasProperty('idusers', $user);

        $users_password_confirm = $this->validation->sha256(self::USERS_PASSWORD);

        $response = $this->passwordManagerModel->updatePasswordDB(
            (new PasswordManager())
                ->setIdusers($user->idusers)
                ->setUsersPasswordConfirm($users_password_confirm)
        );

        $this->assertIsObject($response);
        $this->assertObjectHasProperty('status', $response);
        $this->assertSame(Status::SUCCESS, $response->status);
    }
}
