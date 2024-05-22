<?php

declare(strict_types=1);

namespace Tests\Global\Api\LionDatabase\MySQL;

use Lion\Database\Drivers\Schema\MySQL as Schema;
use Lion\Request\Http;
use Lion\Request\Status;
use Lion\Test\Test;
use Tests\Providers\SetUpMigrationsAndQueuesProviderTrait;

class LoginControllerTest extends Test
{
    use SetUpMigrationsAndQueuesProviderTrait;

    const API_URL = 'http://127.0.0.1:8000/api/auth/login';
    const USERS_EMAIL = 'root@dev.com';
    const USERS_EMAIL_MANAGER = 'manager@dev.com';
    const USERS_PASSWORD = 'fc59487712bbe89b488847b77b5744fb6b815b8fc65ef2ab18149958edb61464';

    protected function setUp(): void
    {
        $this->runMigrationsAndQueues();
    }

    protected function tearDown(): void
    {
        Schema::truncateTable('users')->execute();
    }

    public function testAuth(): void
    {
        $auth = json_decode(
            fetch(Http::HTTP_POST, self::API_URL, [
                'json' => [
                    'users_email' => self::USERS_EMAIL,
                    'users_password' => self::USERS_PASSWORD,
                ]
            ])
                ->getBody()
                ->getContents()
        );

        $this->assertIsObject($auth);
        $this->assertObjectHasProperty('code', $auth);
        $this->assertObjectHasProperty('status', $auth);
        $this->assertObjectHasProperty('message', $auth);
        $this->assertObjectHasProperty('data', $auth);
        $this->assertObjectHasProperty('jwt', $auth->data);
        $this->assertSame(Http::HTTP_OK, $auth->code);
        $this->assertSame(Status::SUCCESS, $auth->status);
        $this->assertSame('successfully authenticated user', $auth->message);
    }

    public function testAuthIncorrect1(): void
    {
        $exception = $this->getExceptionFromApi(function () {
            fetch(Http::HTTP_POST, self::API_URL, [
                'json' => [
                    'users_email' => 'root-dev@dev.com',
                    'users_password' => self::USERS_PASSWORD,
                ]
            ]);
        });

        $this->assertJsonContent($this->getResponse($exception->getMessage(), 'response:'), [
            'code' => Http::HTTP_UNAUTHORIZED,
            'status' => Status::SESSION_ERROR,
            'message' => 'email/password is incorrect [AUTH-1]'
        ]);
    }

    public function testAuthIncorrect2(): void
    {
        $exception = $this->getExceptionFromApi(function () {
            fetch(Http::HTTP_POST, self::API_URL, [
                'json' => [
                    'users_email' => self::USERS_EMAIL,
                    'users_password' => (self::USERS_PASSWORD . '-x'),
                ]
            ]);
        });

        $this->assertJsonContent($this->getResponse($exception->getMessage(), 'response:'), [
            'code' => Http::HTTP_UNAUTHORIZED,
            'status' => Status::ERROR,
            'message' => 'email/password is incorrect [AUTH-2]'
        ]);
    }

    public function testAuthVerifyAccount(): void
    {
        $exception = $this->getExceptionFromApi(function () {
            fetch(Http::HTTP_POST, self::API_URL, [
                'json' => [
                    'users_email' => self::USERS_EMAIL_MANAGER,
                    'users_password' => self::USERS_PASSWORD,
                ]
            ]);
        });

        $this->assertJsonContent($this->getResponse($exception->getMessage(), 'response:'), [
            'code' => Http::HTTP_FORBIDDEN,
            'status' => Status::SESSION_ERROR,
            'message' => "the user's account has not yet been verified",
        ]);
    }
}
