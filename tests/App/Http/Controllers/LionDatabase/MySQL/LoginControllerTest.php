<?php

declare(strict_types=1);

namespace Tests\App\Http\Controllers\LionDatabase\MySQL;

use Lion\Command\Kernel;
use Lion\Database\Drivers\Schema\MySQL as Schema;
use Lion\Request\Request;
use Lion\Request\Response;
use Lion\Route\Route;
use Lion\Test\Test;

class LoginControllerTest extends Test
{
    const API_URL = 'http://127.0.0.1:8000/api/auth/login';
    const API_URL_USERS = 'http://127.0.0.1:8000/api/users';
    const JSON_AUTH = [
        'users_email' => 'root@dev.com',
        'users_password' => 'fc59487712bbe89b488847b77b5744fb6b815b8fc65ef2ab18149958edb61464'
    ];
    const JSON_AUTH_ERR_1 = [
        'users_email' => 'root-dev@dev.com',
        'users_password' => 'fc59487712bbe89b488847b77b5744fb6b815b8fc65ef2ab18149958edb61464'
    ];
    const JSON_AUTH_ERR_2 = [
        'users_email' => 'root@dev.com',
        'users_password' => 'fc59487712bbe89b488847b77b5744fb6b815b8fc65ef2ab18149958edb61464-x'
    ];

    protected function setUp(): void
    {
        (new Kernel)->execute('php lion migrate:fresh --seed', false);
    }

    protected function tearDown(): void
    {
        Schema::truncateTable('users')->execute();
    }

    public function testAuth(): void
    {
        $auth = json_decode(
            fetch(Route::POST, self::API_URL, ['json' => self::JSON_AUTH])
                ->getBody()
                ->getContents()
        );

        $this->assertIsObject($auth);
        $this->assertObjectHasProperty('code', $auth);
        $this->assertObjectHasProperty('status', $auth);
        $this->assertObjectHasProperty('message', $auth);
        $this->assertObjectHasProperty('data', $auth);
        $this->assertObjectHasProperty('jwt', $auth->data);
        $this->assertSame(Request::HTTP_OK, $auth->code);
        $this->assertSame(Response::SUCCESS, $auth->status);
        $this->assertSame('Successfully authenticated user', $auth->message);
    }

    public function testAuthIncorrect1(): void
    {
        $exception = $this->getExceptionFromApi(function () {
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
        $exception = $this->getExceptionFromApi(function () {
            fetch(Route::POST, self::API_URL, ['json' => self::JSON_AUTH_ERR_2]);
        });

        $this->assertJsonContent($this->getResponse($exception->getMessage(), 'response:'), [
            'code' => 500,
            'status' => 'error',
            'message' => 'Email/password is incorrect [AUTH-2]'
        ]);
    }
}
