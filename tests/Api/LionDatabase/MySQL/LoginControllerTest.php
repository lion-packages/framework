<?php

declare(strict_types=1);

namespace Tests\Api\LionDatabase\MySQL;

use Exception;
use Lion\Database\Drivers\Schema\MySQL as Schema;
use Lion\Request\Http;
use Lion\Request\Status;
use Lion\Test\Test;
use Tests\Providers\SetUpMigrationsAndQueuesProviderTrait;

class LoginControllerTest extends Test
{
    use SetUpMigrationsAndQueuesProviderTrait;

    const string USERS_EMAIL = 'root@dev.com';
    const string USERS_EMAIL_MANAGER = 'manager@dev.com';
    const string USERS_PASSWORD = 'fc59487712bbe89b488847b77b5744fb6b815b8fc65ef2ab18149958edb61464';

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
        $response = json_decode(
            fetch(Http::POST, (env('SERVER_URL') . '/api/auth/login'), [
                'json' => [
                    'users_email' => self::USERS_EMAIL,
                    'users_password' => self::USERS_PASSWORD,
                ]
            ])
                ->getBody()
                ->getContents()
        );

        $this->assertIsObject($response);
        $this->assertObjectHasProperty('code', $response);
        $this->assertObjectHasProperty('status', $response);
        $this->assertObjectHasProperty('message', $response);
        $this->assertObjectHasProperty('data', $response);
        $this->assertObjectHasProperty('jwt', $response->data);
        $this->assertSame(Http::OK, $response->code);
        $this->assertSame(Status::SUCCESS, $response->status);
        $this->assertSame('successfully authenticated user', $response->message);
    }

    /**
     * @throws Exception
     */
    public function testAuthIncorrect1(): void
    {
        $exception = $this->getExceptionFromApi(function () {
            fetch(Http::POST, (env('SERVER_URL') . '/api/auth/login'), [
                'json' => [
                    'users_email' => 'root-dev@dev.com',
                    'users_password' => self::USERS_PASSWORD,
                ]
            ]);
        });

        $this->assertJsonContent($this->getResponse($exception->getMessage(), 'response:'), [
            'code' => Http::UNAUTHORIZED,
            'status' => Status::SESSION_ERROR,
            'message' => 'email/password is incorrect [AUTH-1]'
        ]);
    }

    /**
     * @throws Exception
     */
    public function testAuthIncorrect2(): void
    {
        $exception = $this->getExceptionFromApi(function () {
            fetch(Http::POST, (env('SERVER_URL') . '/api/auth/login'), [
                'json' => [
                    'users_email' => self::USERS_EMAIL,
                    'users_password' => (self::USERS_PASSWORD . '-x'),
                ]
            ]);
        });

        $this->assertJsonContent($this->getResponse($exception->getMessage(), 'response:'), [
            'code' => Http::UNAUTHORIZED,
            'status' => Status::ERROR,
            'message' => 'email/password is incorrect [AUTH-2]'
        ]);
    }

    /**
     * @throws Exception
     */
    public function testAuthVerifyAccount(): void
    {
        $exception = $this->getExceptionFromApi(function () {
            fetch(Http::POST, (env('SERVER_URL') . '/api/auth/login'), [
                'json' => [
                    'users_email' => self::USERS_EMAIL_MANAGER,
                    'users_password' => self::USERS_PASSWORD,
                ]
            ]);
        });

        $this->assertJsonContent($this->getResponse($exception->getMessage(), 'response:'), [
            'code' => Http::FORBIDDEN,
            'status' => Status::SESSION_ERROR,
            'message' => "the user's account has not yet been verified",
        ]);
    }
}
