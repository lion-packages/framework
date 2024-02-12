<?php

declare(strict_types=1);

namespace Tests\Models\LionDatabase\MySQL;

use App\Enums\RolesEnum;
use App\Models\LionDatabase\MySQL\UsersModel;
use Database\Class\LionDatabase\MySQL\Users;
use Lion\Database\Drivers\MySQL;
use Lion\Database\Drivers\Schema\MySQL as Schema;
use Lion\Security\Validation;
use Lion\Test\Test;
use Tests\Providers\ConnectionProviderTrait;

class UsersModelTest extends Test
{
    use ConnectionProviderTrait;

    private UsersModel $usersModel;
    private Users $users;

	protected function setUp(): void 
	{
        $this->initConnections();

        $validation = new Validation();
        $this->usersModel = new UsersModel();

        $this->users = (new Users())
            ->setIdusers(1)
            ->setIdroles(1)
            ->setIddocumentTypes(1)
            ->setUsersName('Sergio')
            ->setUsersLastName('Leon')
            ->setUsersEmail(fake()->email())
            ->setUsersPassword($validation->sha256('lion'))
            ->setUsersCode(uniqid('code-'));
	}

	protected function tearDown(): void 
	{
        Schema::truncateTable('users')->execute();
	}

    public function testCreateUsersDB(): void
    {
        $this->assertTrue(isSuccess($this->usersModel->createUsersDB($this->users)));

        $users = MySQL::table('users')
            ->select()
            ->where()->equalTo('users_code', $this->users->getUsersCode())
            ->get();

        $this->assertSame($this->users->getUsersCode(), $users->users_code);
    }

    public function testReadUsersDB(): void
    {
        $this->assertTrue(isSuccess($this->usersModel->createUsersDB($this->users)));

        $users = $this->usersModel->readUsersDB();

        $this->assertIsArray($users);

        $firstUser = reset($users);

        $this->assertSame($this->users->getIdroles(), $firstUser->idroles);
        $this->assertSame($this->users->getIddocumentTypes(), $firstUser->iddocument_types);
        $this->assertSame($this->users->getUsersName(), $firstUser->users_name);
        $this->assertSame($this->users->getUsersLastName(), $firstUser->users_last_name);
        $this->assertSame($this->users->getUsersEmail(), $firstUser->users_email);
        $this->assertSame($this->users->getUsersCode(), $firstUser->users_code);
    }

    public function testReadUsersDBNotAvailableData(): void
    {
        $users = $this->usersModel->readUsersDB();

        $this->assertIsObject($users);
        $this->assertObjectHasProperty('status', $users);
        $this->assertObjectHasProperty('message', $users);
    }

    public function testUpdateUsersDB(): void
    {
        $this->assertTrue(isSuccess($this->usersModel->createUsersDB($this->users)));

        $users = $this->usersModel->readUsersDB();

        $this->assertIsArray($users);

        $firstUser = reset($users);
        $this->users->setIdusers($firstUser->idusers)->setIdroles(RolesEnum::CUSTOMER->value);

        $this->assertTrue(isSuccess($this->usersModel->updateUsersDB($this->users)));

        $users = $this->usersModel->readUsersDB();

        $this->assertIsArray($users);

        $firstUser = reset($users);

        $this->assertSame($this->users->getIdroles(), RolesEnum::CUSTOMER->value);
        $this->assertSame($this->users->getIddocumentTypes(), $firstUser->iddocument_types);
        $this->assertSame($this->users->getUsersName(), $firstUser->users_name);
        $this->assertSame($this->users->getUsersLastName(), $firstUser->users_last_name);
        $this->assertSame($this->users->getUsersEmail(), $firstUser->users_email);
        $this->assertSame($this->users->getUsersCode(), $firstUser->users_code);
    }

    public function testDeleteUsersDB(): void
    {
        $this->assertTrue(isSuccess($this->usersModel->createUsersDB($this->users)));

        $users = $this->usersModel->readUsersDB();

        $this->assertIsArray($users);

        $firstUser = reset($users);

        $this->assertTrue(isSuccess($this->usersModel->deleteUsersDB($this->users->setIdusers($firstUser->idusers))));

        $users = $this->usersModel->readUsersDB();

        $this->assertIsObject($users);
        $this->assertObjectHasProperty('status', $users);
        $this->assertObjectHasProperty('message', $users);
    }
}
