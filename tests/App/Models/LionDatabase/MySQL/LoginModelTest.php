<?php

declare(strict_types=1);

namespace Tests\App\Models\LionDatabase\MySQL;

use App\Models\LionDatabase\MySQL\LoginModel;
use Database\Class\LionDatabase\MySQL\Users;
use Lion\Database\Drivers\Schema\MySQL as Schema;
use Lion\Route\Route;
use Lion\Test\Test;

class LoginModelTest extends Test
{
    const API_URL_USERS = 'http://127.0.0.1:8000/api/users';
    const JSON_AUTH = [
        'users_email' => 'root-sleon@dev.com',
        'users_password' => 'fc59487712bbe89b488847b77b5744fb6b815b8fc65ef2ab18149958edb61464'
    ];
    const JSON_AUTH_ERR_1 = [
        'users_email' => 'sleon@dev.com',
        'users_password' => 'fc59487712bbe89b488847b77b5744fb6b815b8fc65ef2ab18149958edb61464'
    ];
    const JSON_CREATE_USERS = [
        'idroles' => 1,
        'iddocument_types' => 1,
        'users_name' => 'Sergio',
        'users_last_name' => 'Leon',
        ...self::JSON_AUTH
    ];

    private LoginModel $loginModel;
    private Users $users;

    protected function setUp(): void
    {
        $this->loginModel = new LoginModel();

        $this->users = (new Users())
            ->setUsersEmail(self::JSON_AUTH['users_email']);
    }

    protected function tearDown(): void
    {
        Schema::truncateTable('users')->execute();
    }

    private function assertCreateUser(): void
    {
        $response = fetch(Route::POST, self::API_URL_USERS, ['json' => self::JSON_CREATE_USERS])
            ->getBody()
            ->getContents();

        $this->assertJsonContent($response, [
            'status' => 'success',
            'message' => 'Procedure executed successfully'
        ]);
    }

    public function testAuthDB(): void
    {
        $this->assertCreateUser();

        $response = $this->loginModel->authDB($this->users);

        $this->assertSame(1, $response->count);
    }

    public function testAuthEmptyDB(): void
    {
        $this->assertCreateUser();

        $response = $this->loginModel->authDB($this->users->setUsersEmail(self::JSON_AUTH_ERR_1['users_email']));

        $this->assertSame(0, $response->count);
    }

    public function testSessionDB(): void
    {
        $this->assertCreateUser();

        $response = $this->loginModel->sessionDB($this->users);

        $this->assertInstanceOf(Users::class, $response);
        $this->assertSame($this->users->getUsersEmail(), $response->getUsersEmail());
    }
}
