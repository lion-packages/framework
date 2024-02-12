<?php

declare(strict_types=1);

namespace Tests\Controllers\LionDatabase\MySQL;

use Lion\Database\Drivers\Schema\MySQL as Schema;
use Lion\Route\Route;
use Lion\Test\Test;
use Tests\Providers\ConnectionProviderTrait;
use Tests\Providers\JsonProviderTrait;

class UsersControllerTest extends Test
{
    use ConnectionProviderTrait;
    use JsonProviderTrait;

    const API_URL = 'http://127.0.0.1:8000/api/users';
    const JSON_CREATE_USERS = [
        'idroles' => 1,
        'iddocumentTypes' => 1,
        'usersName' => 'Sergio',
        'usersLastName' => 'Leon',
        'usersEmail' => 'sleon@dev.com',
        'usersPassword' => 'cbfad02f9ed2a8d1e08d8f74f5303e9eb93637d47f82ab6f1c15871cf8dd0481'
    ];
    const JSON_UPDATE_USERS = [
        'idroles' => 1,
        'iddocumentTypes' => 1,
        'usersName' => 'Sergio D',
        'usersLastName' => 'Leon G',
        'usersEmail' => 'sleon@dev.com'
    ];

	protected function setUp(): void 
	{
        $this->initConnections();
	}

    protected function tearDown(): void
    {
        Schema::truncateTable('users')->execute();
    }

    public function testCreateUsers(): void
    {
        $response = fetch(Route::POST, self::API_URL, ['json' => self::JSON_CREATE_USERS])->getBody()->getContents();

        $this->assertFetchJson($this, $response, [
            'status' => 'success',
            'message' => 'Procedure executed successfully'
        ]);
    }

    public function testReadUsers(): void
    {
        $response = fetch(Route::POST, self::API_URL, ['json' => self::JSON_CREATE_USERS])->getBody()->getContents();

        $this->assertFetchJson($this, $response, [
            'status' => 'success',
            'message' => 'Procedure executed successfully'
        ]);

        $users = json_decode(fetch(Route::GET, self::API_URL)->getBody()->getContents(), true);

        $this->assertIsArray($users);
        $this->assertCount(1, $users);
    }

    public function testReadUsersWithoutData(): void
    {
        $users = fetch(Route::GET, self::API_URL)->getBody()->getContents();

        $this->assertFetchJson($this, $users, [
            'status' => 'success',
            'message' => 'No data available'
        ]);
    }

    public function testUpdateUsers(): void
    {
        $response = fetch(Route::POST, self::API_URL, ['json' => self::JSON_CREATE_USERS])->getBody()->getContents();

        $this->assertFetchJson($this, $response, [
            'status' => 'success',
            'message' => 'Procedure executed successfully'
        ]);

        $users = json_decode(fetch(Route::GET, self::API_URL)->getBody()->getContents(), true);

        $this->assertIsArray($users);
        $this->assertCount(1, $users);

        $firstUser = (object) reset($users);

        $response = fetch(Route::PUT, self::API_URL . '/' . $firstUser->idusers, ['json' => self::JSON_UPDATE_USERS])
            ->getBody()
            ->getContents();

        $this->assertFetchJson($this, $response, [
            'status' => 'success',
            'message' => 'Procedure executed successfully'
        ]);

        $users = json_decode(fetch(Route::GET, self::API_URL)->getBody()->getContents(), true);

        $this->assertIsArray($users);
        $this->assertCount(1, $users);

        $firstUser = (object) reset($users);

        $this->assertSame(self::JSON_UPDATE_USERS['usersName'], $firstUser->users_name);
        $this->assertSame(self::JSON_UPDATE_USERS['usersLastName'], $firstUser->users_last_name);
    }

    public function testDeleteUsers(): void
    {
        $response = fetch(Route::POST, self::API_URL, ['json' => self::JSON_CREATE_USERS])->getBody()->getContents();

        $this->assertFetchJson($this, $response, [
            'status' => 'success',
            'message' => 'Procedure executed successfully'
        ]);

        $users = json_decode(fetch(Route::GET, self::API_URL)->getBody()->getContents(), true);

        $this->assertIsArray($users);
        $this->assertCount(1, $users);

        $firstUser = (object) reset($users);
        $response = fetch(Route::DELETE, self::API_URL . '/' . $firstUser->idusers)->getBody()->getContents();

        $this->assertFetchJson($this, $response, [
            'status' => 'success',
            'message' => 'Procedure executed successfully'
        ]);

        $users = fetch(Route::GET, self::API_URL)->getBody()->getContents();

        $this->assertFetchJson($this, $users, [
            'status' => 'success',
            'message' => 'No data available'
        ]);
    }
}
