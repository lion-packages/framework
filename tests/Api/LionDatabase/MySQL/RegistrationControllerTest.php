<?php

declare(strict_types=1);

namespace Tests\Api\LionDatabase\MySQL;

use Exception;
use Lion\Database\Drivers\MySQL as DB;
use Lion\Database\Drivers\Schema\MySQL as Schema;
use Lion\Request\Http;
use Lion\Request\Status;
use Lion\Test\Test;
use Tests\Providers\SetUpMigrationsAndQueuesProviderTrait;

class RegistrationControllerTest extends Test
{
    use SetUpMigrationsAndQueuesProviderTrait;

    const string API_URL = 'http://127.0.0.1:8000/api/auth';
    const string USERS_EMAIL = 'root@dev.com';
    const string USERS_PASSWORD = 'fc59487712bbe89b488847b77b5744fb6b815b8fc65ef2ab18149958edb61464';

    protected function setUp(): void
    {
        $this->runMigrationsAndQueues(true);

        Schema::truncateTable('users')->execute();
    }

    protected function tearDown(): void
    {
        Schema::truncateTable('users')->execute();

        Schema::truncateTable('task_queue')->execute();
    }

    public function testRegister(): void
    {
        $response = fetch(Http::POST, (self::API_URL . '/register'), [
            'json' => [
                'users_email' => self::USERS_EMAIL,
                'users_password' => self::USERS_PASSWORD,
            ]
        ])
            ->getBody()
            ->getContents();

        $this->assertJsonContent($response, [
            'code' => Http::OK,
            'status' => Status::SUCCESS,
            'message' => 'user successfully registered, check your mailbox to obtain the account activation code',
        ]);
    }

    /**
     * @throws Exception
     */
    public function testRegisterRegistered(): void
    {
        $response = fetch(Http::POST, (self::API_URL . '/register'), [
            'json' => [
                'users_email' => self::USERS_EMAIL,
                'users_password' => self::USERS_PASSWORD,
            ]
        ])
            ->getBody()
            ->getContents();

        $this->assertJsonContent($response, [
            'code' => Http::OK,
            'status' => Status::SUCCESS,
            'message' => 'user successfully registered, check your mailbox to obtain the account activation code',
        ]);

        $exception = $this->getExceptionFromApi(function () {
            fetch(Http::POST, (self::API_URL . '/register'), [
                'json' => [
                    'users_email' => self::USERS_EMAIL,
                    'users_password' => self::USERS_PASSWORD,
                ]
            ]);
        });

        $this->assertJsonContent($this->getResponse($exception->getMessage(), 'response:'), [
            'code' => Http::BAD_REQUEST,
            'status' => Status::ERROR,
            'message' => 'there is already an account registered with this email',
        ]);
    }

    public function testVerifyAccount(): void
    {
        $response = fetch(Http::POST, (self::API_URL . '/register'), [
            'json' => [
                'users_email' => self::USERS_EMAIL,
                'users_password' => self::USERS_PASSWORD,
            ]
        ])
            ->getBody()
            ->getContents();

        $this->assertJsonContent($response, [
            'code' => Http::OK,
            'status' => Status::SUCCESS,
            'message' => 'user successfully registered, check your mailbox to obtain the account activation code',
        ]);

        $users_activation_code = DB::table('users')
            ->select('users_activation_code')
            ->where()->equalTo('users_email', self::USERS_EMAIL)
            ->get();

        $response = fetch(Http::POST, (self::API_URL . '/verify'), [
            'json' => [
                'users_email' => self::USERS_EMAIL,
                'users_activation_code' => $users_activation_code->users_activation_code
            ]
        ])
            ->getBody()
            ->getContents();

        $this->assertJsonContent($response, [
            'code' => Http::OK,
            'status' => Status::SUCCESS,
            'message' => 'user account has been successfully verified'
        ]);
    }

    /**
     * @throws Exception
     */
    public function testVerifyAccountInvalid1(): void
    {
        $response = fetch(Http::POST, (self::API_URL . '/register'), [
            'json' => [
                'users_email' => self::USERS_EMAIL,
                'users_password' => self::USERS_PASSWORD,
            ]
        ])
            ->getBody()
            ->getContents();

        $this->assertJsonContent($response, [
            'code' => Http::OK,
            'status' => Status::SUCCESS,
            'message' => 'user successfully registered, check your mailbox to obtain the account activation code',
        ]);

        $exception = $this->getExceptionFromApi(function () {
            fetch(Http::POST, (self::API_URL . '/verify'), [
                'json' => [
                    'users_activation_code' => fake()->numerify('######'),
                    'users_email' => fake()->email()
                ]
            ]);
        });

        $this->assertJsonContent($this->getResponse($exception->getMessage(), 'response:'), [
            'code' => Http::FORBIDDEN,
            'status' => Status::SESSION_ERROR,
            'message' => 'verification code is invalid [ERR-1]',
        ]);
    }

    /**
     * @throws Exception
     */
    public function testVerifyAccountInvalid2(): void
    {
        $response = fetch(Http::POST, (self::API_URL . '/register'), [
            'json' => [
                'users_email' => self::USERS_EMAIL,
                'users_password' => self::USERS_PASSWORD,
            ]
        ])
            ->getBody()
            ->getContents();

        $this->assertJsonContent($response, [
            'code' => Http::OK,
            'status' => Status::SUCCESS,
            'message' => 'user successfully registered, check your mailbox to obtain the account activation code',
        ]);

        $exception = $this->getExceptionFromApi(function () {
            fetch(Http::POST, (self::API_URL . '/verify'), [
                'json' => [
                    'users_email' => self::USERS_EMAIL,
                    'users_activation_code' => fake()->numerify('######'),
                ]
            ]);
        });

        $this->assertJsonContent($this->getResponse($exception->getMessage(), 'response:'), [
            'code' => Http::FORBIDDEN,
            'status' => Status::SESSION_ERROR,
            'message' => 'verification code is invalid [ERR-2]',
        ]);
    }
}
