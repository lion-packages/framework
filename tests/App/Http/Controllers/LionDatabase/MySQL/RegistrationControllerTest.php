<?php

declare(strict_types=1);

namespace Tests\App\Http\Controllers\LionDatabase\MySQL;

use Lion\Database\Drivers\MySQL as DB;
use Lion\Database\Drivers\Schema\MySQL as Schema;
use Lion\Request\Request;
use Lion\Request\Response;
use Lion\Route\Route;
use Lion\Test\Test;
use Tests\Providers\SetUpMigrationsAndQueuesProviderTrait;

class RegistrationControllerTest extends Test
{
    use SetUpMigrationsAndQueuesProviderTrait;

    const API_URL = 'http://127.0.0.1:8000/api/auth';

    protected function setUp(): void
    {
        $this->runMigrationsAndQueues(false);
    }

    protected function tearDown(): void
    {
        Schema::truncateTable('users')->execute();

        Schema::truncateTable('task_queue')->execute();
    }

    public function testRegister(): void
    {
        $response = fetch(Route::POST, (self::API_URL . '/register'), [
            'json' => [
                'users_email' => 'root@dev.com',
                'users_password' => 'fc59487712bbe89b488847b77b5744fb6b815b8fc65ef2ab18149958edb61464'
            ]
        ])
            ->getBody()
            ->getContents();

        $this->assertJsonContent($response, [
            'status' => 'success',
            'message' => 'Procedure executed successfully'
        ]);
    }

    public function testVerifyAccount(): void
    {
        $response = fetch(Route::POST, (self::API_URL . '/register'), [
            'json' => [
                'users_email' => 'root@dev.com',
                'users_password' => 'fc59487712bbe89b488847b77b5744fb6b815b8fc65ef2ab18149958edb61464'
            ]
        ])
            ->getBody()
            ->getContents();

        $this->assertJsonContent($response, [
            'status' => 'success',
            'message' => 'Procedure executed successfully'
        ]);

        $users_activation_code = DB::table('users')
            ->select('users_activation_code')
            ->where()->equalTo('users_email', 'root@dev.com')
            ->get();

        $response = fetch(Route::POST, (self::API_URL . '/verify'), [
            'json' => [
                'users_email' => 'root@dev.com',
                'users_activation_code' => $users_activation_code->users_activation_code
            ]
        ])
            ->getBody()
            ->getContents();

        $this->assertJsonContent($response, [
            'code' => Request::HTTP_OK,
            'status' => Response::SUCCESS,
            'message' => 'user account has been successfully verified'
        ]);
    }
}
