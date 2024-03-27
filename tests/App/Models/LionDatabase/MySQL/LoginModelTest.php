<?php

declare(strict_types=1);

namespace Tests\App\Models\LionDatabase\MySQL;

use App\Models\LionDatabase\MySQL\LoginModel;
use App\Models\LionDatabase\MySQL\UsersModel;
use Database\Class\LionDatabase\MySQL\Users;
use Lion\Bundle\Interface\CapsuleInterface;
use Lion\Database\Drivers\MySQL as DB;
use Lion\Database\Drivers\Schema\MySQL as Schema;
use Lion\Test\Test;

class LoginModelTest extends Test
{
    const USERS_EMAIL = 'root-sleon@dev.com';
    const USERS_EMAIL_ERR = 'sleon@dev.com';

    private LoginModel $loginModel;

    protected function setUp(): void
    {
        $this->loginModel = new LoginModel();
    }

    protected function tearDown(): void
    {
        Schema::truncateTable('users')->execute();
    }

    private function assertCreateUser(): void
    {
        $users = (new Users())
            ->setIdusers(1)
            ->setIdroles(1)
            ->setIddocumentTypes(1)
            ->setUsersName('Sergio')
            ->setUsersLastName('Leon')
            ->setUsersEmail(self::USERS_EMAIL)
            ->setUsersPassword('cbfad02f9ed2a8d1e08d8f74f5303e9eb93637d47f82ab6f1c15871cf8dd0481')
            ->setUsersCode(uniqid('code-'));

        $this->assertTrue(isSuccess((new UsersModel)->createUsersDB($users)));

        $data = DB::table('users')
            ->select()
            ->where()->equalTo('users_code', $users->getUsersCode())
            ->get();

        $this->assertSame($users->getUsersCode(), $data->users_code);
    }

    public function testAuthDB(): void
    {
        $this->assertCreateUser();

        $response = $this->loginModel->authDB(
            (new Users())
                ->setUsersEmail(self::USERS_EMAIL)
        );

        $this->assertIsObject($response);
        $this->assertSame(1, $response->count);
    }

    public function testAuthEmptyDB(): void
    {
        $this->assertCreateUser();

        $response = $this->loginModel->authDB(
            (new Users())
                ->setUsersEmail(self::USERS_EMAIL_ERR)
        );

        $this->assertSame(0, $response->count);
    }

    public function testSessionDB(): void
    {
        $this->assertCreateUser();

        $users = (new Users())
            ->setUsersEmail(self::USERS_EMAIL);

        $response = $this->loginModel->sessionDB($users);

        $this->assertInstances($response, [Users::class, CapsuleInterface::class]);
        $this->assertSame($users->getUsersEmail(), $response->getUsersEmail());
    }
}
