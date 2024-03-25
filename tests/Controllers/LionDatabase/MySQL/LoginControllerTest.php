<?php

declare(strict_types=1);

namespace Tests\Controllers\LionDatabase\MySQL;

use Closure;
use Exception;
use Lion\Database\Drivers\Schema\MySQL as Schema;
use Lion\Route\Route;
use Lion\Test\Test;

class LoginControllerTest extends Test
{
    const API_URL = 'http://127.0.0.1:8000/api/auth';
    const API_URL_USERS = 'http://127.0.0.1:8000/api/users';
    const JSON_AUTH = [
        'users_email' => 'root-sleon@dev.com',
        'users_password' => 'fc59487712bbe89b488847b77b5744fb6b815b8fc65ef2ab18149958edb61464'
    ];
    const JSON_AUTH_ERR_1 = [
        'users_email' => 'sleon@dev.com',
        'users_password' => 'fc59487712bbe89b488847b77b5744fb6b815b8fc65ef2ab18149958edb61464'
    ];
    const JSON_AUTH_ERR_2 = [
        'users_email' => 'root-sleon@dev.com',
        'users_password' => 'fc59487712bbe89b488847b77b5744fb6b815b8fc65ef2ab18149958edb61464-x'
    ];
    const JSON_CREATE_USERS = [
        'idroles' => 1,
        'iddocument_types' => 1,
        'users_name' => 'Sergio',
        'users_last_name' => 'Leon',
        ...self::JSON_AUTH
    ];

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

    public function testAuth(): void
    {
        $this->assertCreateUser();

        $auth = fetch(Route::POST, self::API_URL, ['json' => self::JSON_AUTH])->getBody()->getContents();

        $this->assertJsonContent($auth, [
            'code' => 200,
            'status' => 'success',
            'message' => 'Successfully authenticated user'
        ]);
    }

    public function testAuthIncorrect1(): void
    {
        $this->assertCreateUser();

        $exception = $this->getExceptionFromApi(function() {
            fetch(Route::POST, self::API_URL, ['json' => self::JSON_AUTH_ERR_1]);
        });

        $this->assertJsonContent($this->getResponse($exception->getMessage(), 'response:'), [
            'code' => 500,
            'status' => 'error',
            'message' => 'Email/password is incorrect [AUTH-1]'
        ]);
    }

    public function testAuthIncorrect2(): void
    {
        $this->assertCreateUser();

        $exception = $this->getExceptionFromApi(function() {
            fetch(Route::POST, self::API_URL, ['json' => self::JSON_AUTH_ERR_2]);
        });

        $this->assertJsonContent($this->getResponse($exception->getMessage(), 'response:'), [
            'code' => 500,
            'status' => 'error',
            'message' => 'Email/password is incorrect [AUTH-2]'
        ]);
    }
}
