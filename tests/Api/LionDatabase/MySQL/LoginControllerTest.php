<?php

declare(strict_types=1);

namespace Tests\Api\LionDatabase\MySQL;

use Database\Factory\LionDatabase\MySQL\UsersFactory;
use Exception;
use Lion\Database\Drivers\Schema\MySQL as Schema;
use Lion\Request\Http;
use Lion\Request\Status;
use Lion\Test\Test;
use Tests\Providers\AuthJwtProviderTrait;
use Tests\Providers\SetUpMigrationsAndQueuesProviderTrait;

class LoginControllerTest extends Test
{
    use AuthJwtProviderTrait;
    use SetUpMigrationsAndQueuesProviderTrait;

    const string USERS_EMAIL_MANAGER = 'manager@dev.com';

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
        $encode = $this->AESEncode(['users_password' => UsersFactory::USERS_PASSWORD]);

        $response = json_decode(
            fetch(Http::POST, (env('SERVER_URL') . '/api/auth/login'), [
                'json' => [
                    'users_email' => UsersFactory::USERS_EMAIL,
                    'users_password' => $encode['users_password'],
                ],
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
        $exception = $this->getExceptionFromApi(function (): void {
            $encode = $this->AESEncode(['users_password' => UsersFactory::USERS_PASSWORD]);

            fetch(Http::POST, (env('SERVER_URL') . '/api/auth/login'), [
                'json' => [
                    'users_email' => 'root-dev@dev.com',
                    'users_password' => $encode['users_password'],
                ],
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
        $exception = $this->getExceptionFromApi(function (): void {
            $encode = $this->AESEncode(['users_password' => UsersFactory::USERS_PASSWORD]);

            fetch(Http::POST, (env('SERVER_URL') . '/api/auth/login'), [
                'json' => [
                    'users_email' => UsersFactory::USERS_EMAIL,
                    'users_password' => "{$encode['users_password']}-x",
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
            $encode = $this->AESEncode(['users_password' => UsersFactory::USERS_PASSWORD]);

            fetch(Http::POST, (env('SERVER_URL') . '/api/auth/login'), [
                'json' => [
                    'users_email' => self::USERS_EMAIL_MANAGER,
                    'users_password' => $encode['users_password'],
                ],
            ]);
        });

        $this->assertJsonContent($this->getResponse($exception->getMessage(), 'response:'), [
            'code' => Http::FORBIDDEN,
            'status' => Status::SESSION_ERROR,
            'message' => "the user's account has not yet been verified",
        ]);
    }
}
