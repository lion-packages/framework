<?php

declare(strict_types=1);

namespace Tests\App\Models\LionDatabase\MySQL;

use App\Enums\DocumentTypesEnum;
use App\Enums\RolesEnum;
use App\Models\LionDatabase\MySQL\UsersModel;
use Database\Class\LionDatabase\MySQL\Users;
use Database\Factory\LionDatabase\MySQL\UsersFactory;
use Lion\Database\Drivers\MySQL as DB;
use Lion\Database\Drivers\Schema\MySQL as Schema;
use Lion\Test\Test;

class UsersModelTest extends Test
{
    private UsersModel $usersModel;
    private Users $users;

    protected function setUp(): void
    {
        $this->usersModel = new UsersModel();

        $this->users = (new Users())
            ->setIdusers(1)
            ->setIdroles(RolesEnum::ADMINISTRATOR->value)
            ->setIddocumentTypes(DocumentTypesEnum::PASSPORT->value)
            ->setUsersCitizenIdentification(fake()->numerify('##########'))
            ->setUsersName('Sergio')
            ->setUsersLastName('Leon')
            ->setUsersEmail(fake()->email())
            ->setUsersPassword('cbfad02f9ed2a8d1e08d8f74f5303e9eb93637d47f82ab6f1c15871cf8dd0481')
            ->setUsersActivationCode(fake()->numerify('######'))
            ->setUsersRecoveryCode(null)
            ->setUsersCode(uniqid('code-'))
            ->setUsers2fa(UsersFactory::DISABLED_2FA);
    }

    protected function tearDown(): void
    {
        Schema::truncateTable('users')->execute();
    }

    public function testCreateUsersDB(): void
    {
        $this->assertTrue(isSuccess($this->usersModel->createUsersDB($this->users)));

        $users = DB::table('users')
            ->select()
            ->where()->equalTo('users_code', $this->users->getUsersCode())
            ->get();

        $this->assertSame($this->users->getIdroles(), $users->idroles);
        $this->assertSame($this->users->getIddocumentTypes(), $users->iddocument_types);
        $this->assertSame($this->users->getUsersCitizenIdentification(), $users->users_citizen_identification);
        $this->assertSame($this->users->getUsersName(), $users->users_name);
        $this->assertSame($this->users->getUsersLastName(), $users->users_last_name);
        $this->assertSame($this->users->getUsersEmail(), $users->users_email);
        $this->assertSame($this->users->getUsersPassword(), $users->users_password);
        $this->assertSame($this->users->getUsersActivationCode(), $users->users_activation_code);
        $this->assertSame($this->users->getUsersCode(), $users->users_code);
    }

    public function testReadUsersDB(): void
    {
        $this->assertTrue(isSuccess($this->usersModel->createUsersDB($this->users)));

        $users = $this->usersModel->readUsersDB();

        $this->assertIsArray($users);

        $firstUser = reset($users);

        $this->assertSame($this->users->getUsersName(), $firstUser->users_name);
        $this->assertSame($this->users->getUsersLastName(), $firstUser->users_last_name);
    }

    public function testReadUsersDBNotAvailableData(): void
    {
        $response = $this->usersModel->readUsersDB();

        $this->assertIsObject($response);
        $this->assertObjectHasProperty('status', $response);
        $this->assertObjectHasProperty('message', $response);
    }

    public function testReadUsersByIdDB(): void
    {
        $this->assertTrue(isSuccess($this->usersModel->createUsersDB($this->users)));

        $users = $this->usersModel->readUsersByIdDB($this->users);

        $this->assertIsObject($users);

        $this->assertSame($this->users->getUsersName(), $users->users_name);
        $this->assertSame($this->users->getUsersLastName(), $users->users_last_name);
    }

    public function testReadUsersByIdDBNotAvailableData(): void
    {
        $response = $this->usersModel->readUsersByIdDB($this->users);

        $this->assertIsObject($response);
        $this->assertObjectHasProperty('status', $response);
        $this->assertObjectHasProperty('message', $response);
    }

    public function testReadUsersByEmailDB(): void
    {
        $this->assertTrue(isSuccess($this->usersModel->createUsersDB($this->users)));

        $users = $this->usersModel->readUsersByEmailDB($this->users);

        $this->assertIsObject($users);

        $this->assertSame($this->users->getUsersName(), $users->users_name);
        $this->assertSame($this->users->getUsersLastName(), $users->users_last_name);
    }

    public function testReadUsersByEmailDBNotAvailableData(): void
    {
        $response = $this->usersModel->readUsersByEmailDB($this->users);

        $this->assertIsObject($response);
        $this->assertObjectHasProperty('status', $response);
        $this->assertObjectHasProperty('message', $response);
    }

    public function testUpdateUsersDB(): void
    {
        $this->assertTrue(isSuccess($this->usersModel->createUsersDB($this->users)));

        $users = $this->usersModel->readUsersDB();

        $this->assertIsArray($users);

        $firstUser = reset($users);

        $this->assertTrue(isSuccess($this->usersModel->updateUsersDB($this->users->setIdusers($firstUser->idusers))));

        $users = $this->usersModel->readUsersDB();

        $this->assertIsArray($users);

        $firstUser = reset($users);

        $this->assertSame($this->users->getUsersName(), $firstUser->users_name);
        $this->assertSame($this->users->getUsersLastName(), $firstUser->users_last_name);
    }

    public function testUpdateActivationCodeDB(): void
    {
        $this->assertTrue(isSuccess($this->usersModel->createUsersDB($this->users)));

        $user = $this->usersModel->readUsersByEmailDB($this->users);

        $this->assertIsObject($user);
        $this->assertObjectHasProperty('idusers', $user);

        $code = fake()->numerify('######');

        $response = $this->usersModel->updateActivationCodeDB(
            $this->users
                ->setIdusers($user->idusers)
                ->setUsersActivationCode($code)
        );

        $this->assertIsObject($response);
        $this->assertObjectHasProperty('status', $response);
        $this->assertObjectHasProperty('message', $response);

        $user = $this->usersModel->readUsersByEmailDB($this->users);

        $this->assertIsObject($user);
        $this->assertObjectHasProperty('idusers', $user);
        $this->assertObjectHasProperty('users_activation_code', $user);
        $this->assertSame($code, $user->users_activation_code);
    }

    public function testUpdateRecoveryCodeDB(): void
    {
        $this->assertTrue(isSuccess($this->usersModel->createUsersDB($this->users)));

        $user = $this->usersModel->readUsersByEmailDB($this->users);

        $this->assertIsObject($user);
        $this->assertObjectHasProperty('idusers', $user);

        $code = fake()->numerify('######');

        $response = $this->usersModel->updateRecoveryCodeDB(
            $this->users
                ->setIdusers($user->idusers)
                ->setUsersRecoveryCode($code)
        );

        $this->assertIsObject($response);
        $this->assertObjectHasProperty('status', $response);
        $this->assertObjectHasProperty('message', $response);

        $user = $this->usersModel->readUsersByEmailDB($this->users);

        $this->assertIsObject($user);
        $this->assertObjectHasProperty('idusers', $user);
        $this->assertObjectHasProperty('users_recovery_code', $user);
        $this->assertSame($code, $user->users_recovery_code);
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
