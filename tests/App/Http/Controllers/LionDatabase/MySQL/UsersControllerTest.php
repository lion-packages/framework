<?php

declare(strict_types=1);

namespace Tests\App\Http\Controllers\LionDatabase\MySQL;

use Lion\Database\Drivers\Schema\MySQL as Schema;
use Lion\Route\Route;
use Lion\Test\Test;

class UsersControllerTest extends Test
{
    const API_URL = 'http://127.0.0.1:8000/api/users';
    const JSON_CREATE_USERS = [
        'idroles' => 1,
        'iddocument_types' => 1,
        'users_name' => 'Sergio',
        'users_last_name' => 'Leon',
        'users_email' => 'sleon@dev.com',
        'users_password' => 'cbfad02f9ed2a8d1e08d8f74f5303e9eb93637d47f82ab6f1c15871cf8dd0481'
    ];
    const JSON_UPDATE_USERS = [
        'idroles' => 1,
        'iddocument_types' => 1,
        'users_name' => 'Sergio D',
        'users_last_name' => 'Leon G',
        'users_email' => 'sleon@dev.com'
    ];

    protected function tearDown(): void
    {
        Schema::truncateTable('users')->execute();
    }

    private function assertCreateUsers(): void
    {
        $response = fetch(Route::POST, self::API_URL, ['json' => self::JSON_CREATE_USERS])->getBody()->getContents();

        $this->assertJsonContent($response, [
            'status' => 'success',
            'message' => 'Procedure executed successfully'
        ]);
    }

    public function testCreateUsers(): void
    {
        $this->assertCreateUsers();
    }

    public function testReadUsers(): void
    {
        $this->assertCreateUsers();

        $users = json_decode(fetch(Route::GET, self::API_URL)->getBody()->getContents(), true);

        $this->assertIsArray($users);
        $this->assertCount(1, $users);
    }

    public function testReadUsersWithoutData(): void
    {
        $users = fetch(Route::GET, self::API_URL)->getBody()->getContents();

        $this->assertJsonContent($users, [
            'status' => 'success',
            'message' => 'No data available'
        ]);
    }

    public function testUpdateUsers(): void
    {
        $this->assertCreateUsers();

        $users = json_decode(fetch(Route::GET, self::API_URL)->getBody()->getContents(), true);

        $this->assertIsArray($users);
        $this->assertCount(1, $users);

        $firstUser = (object) reset($users);

        $response = fetch(Route::PUT, self::API_URL . '/' . $firstUser->idusers, ['json' => self::JSON_UPDATE_USERS])
            ->getBody()
            ->getContents();

        $this->assertJsonContent($response, [
            'status' => 'success',
            'message' => 'Procedure executed successfully'
        ]);

        $users = json_decode(fetch(Route::GET, self::API_URL)->getBody()->getContents(), true);

        $this->assertIsArray($users);
        $this->assertCount(1, $users);

        $firstUser = (object) reset($users);

        $this->assertSame(self::JSON_UPDATE_USERS['users_name'], $firstUser->users_name);
        $this->assertSame(self::JSON_UPDATE_USERS['users_last_name'], $firstUser->users_last_name);
    }

    public function testDeleteUsers(): void
    {
        $this->assertCreateUsers();

        $users = json_decode(fetch(Route::GET, self::API_URL)->getBody()->getContents(), true);

        $this->assertIsArray($users);
        $this->assertCount(1, $users);

        $firstUser = (object) reset($users);
        $response = fetch(Route::DELETE, self::API_URL . '/' . $firstUser->idusers)->getBody()->getContents();

        $this->assertJsonContent($response, [
            'status' => 'success',
            'message' => 'Procedure executed successfully'
        ]);

        $users = fetch(Route::GET, self::API_URL)->getBody()->getContents();

        $this->assertJsonContent($users, [
            'status' => 'success',
            'message' => 'No data available'
        ]);
    }
}
