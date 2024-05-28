<?php

declare(strict_types=1);

namespace Tests\Api\LionDatabase\MySQL;

use App\Models\LionDatabase\MySQL\UsersModel;
use Database\Class\LionDatabase\MySQL\Users;
use Database\Factory\LionDatabase\MySQL\UsersFactory;
use Exception;
use Lion\Database\Drivers\Schema\MySQL as Schema;
use Lion\Request\Http;
use Lion\Request\Status;
use Lion\Test\Test;
use Tests\Providers\AuthJwtProviderTrait;
use Tests\Providers\SetUpMigrationsAndQueuesProviderTrait;

class PasswordManagerControllerTest extends Test
{
    use AuthJwtProviderTrait;
    use SetUpMigrationsAndQueuesProviderTrait;

    const string USERS_EMAIL = 'root@dev.com';
    const string USERS_PASSWORD = 'lion-password';

    protected function setUp(): void
    {
        $this->runMigrationsAndQueues();
    }

    protected function tearDown(): void
    {
        Schema::truncateTable('users')->execute();

        Schema::truncateTable('task_queue')->execute();
    }

    public function testRecoveryPassword(): void
    {
        $response = fetch(Http::POST, (env('SERVER_URL') . '/api/auth/recovery/password'), [
            'json' => [
                'users_email' => self::USERS_EMAIL,
            ],
        ])
            ->getBody()
            ->getContents();

        $this->assertJsonContent($response, [
            'code' => Http::OK,
            'status' => Status::SUCCESS,
            'message' => 'confirmation code sent, check your email inbox to see your verification code',
        ]);
    }

    /**
     * @throws Exception
     */
    public function testRecoveryPasswordCodeNotNull(): void
    {
        $response = fetch(Http::POST, (env('SERVER_URL') . '/api/auth/recovery/password'), [
            'json' => [
                'users_email' => self::USERS_EMAIL,
            ],
        ])
            ->getBody()
            ->getContents();

        $this->assertJsonContent($response, [
            'code' => Http::OK,
            'status' => Status::SUCCESS,
            'message' => 'confirmation code sent, check your email inbox to see your verification code',
        ]);

        $exception = $this->getExceptionFromApi(function () {
            fetch(Http::POST, (env('SERVER_URL') . '/api/auth/recovery/password'), [
                'json' => [
                    'users_email' => self::USERS_EMAIL,
                ],
            ]);
        });

        $this->assertJsonContent($this->getResponse($exception->getMessage(), 'response:'), [
            'code' => Http::FORBIDDEN,
            'status' => Status::ERROR,
            'message' => 'a verification code has already been sent to this account',
        ]);
    }

    /**
     * @throws Exception
     */
    public function testRecoveryPasswordIncorrect1(): void
    {
        $exception = $this->getExceptionFromApi(function () {
            fetch(Http::POST, (env('SERVER_URL') . '/api/auth/recovery/password'), [
                'json' => [
                    'users_email' => fake()->email(),
                ],
            ]);
        });

        $this->assertJsonContent($this->getResponse($exception->getMessage(), 'response:'), [
            'code' => Http::UNAUTHORIZED,
            'status' => Status::SESSION_ERROR,
            'message' => 'email/password is incorrect [AUTH-1]',
        ]);
    }

    public function testUpdateLostPassword(): void
    {
        $response = fetch(Http::POST, (env('SERVER_URL') . '/api/auth/recovery/password'), [
            'json' => [
                'users_email' => self::USERS_EMAIL,
            ],
        ])
            ->getBody()
            ->getContents();

        $this->assertJsonContent($response, [
            'code' => Http::OK,
            'status' => Status::SUCCESS,
            'message' => 'confirmation code sent, check your email inbox to see your verification code',
        ]);

        $user = (new UsersModel())->readUsersByEmailDB(
            (new Users)
                ->setUsersEmail(self::USERS_EMAIL)
        );

        $this->assertIsObject($user);
        $this->assertObjectHasProperty('users_recovery_code', $user);

        $encode = $this->AESEncode([
            'users_password_new' => self::USERS_PASSWORD,
            'users_password_confirm' => self::USERS_PASSWORD,
        ]);

        $response = fetch(Http::POST, (env('SERVER_URL') . '/api/auth/recovery/verify-code'), [
            'json' => [
                'users_email' => self::USERS_EMAIL,
                'users_password_new' => $encode['users_password_new'],
                'users_password_confirm' => $encode['users_password_confirm'],
                'users_recovery_code' => $user->users_recovery_code,
            ],
        ])
            ->getBody()
            ->getContents();

        $this->assertJsonContent($response, [
            'code' => Http::OK,
            'status' => Status::SUCCESS,
            'message' => 'the recovery code is valid, your password has been updated successfully',
        ]);

        $user = (new UsersModel())->readUsersByEmailDB(
            (new Users)
                ->setUsersEmail(self::USERS_EMAIL)
        );

        $this->assertIsObject($user);
        $this->assertObjectHasProperty('users_recovery_code', $user);
        $this->assertNull($user->users_recovery_code);
    }

    /**
     * @throws Exception
     */
    public function testUpdateLostPasswordIncorrect1(): void
    {
        $exception = $this->getExceptionFromApi(function () {
            $encode = $this->AESEncode([
                'users_password_new' => self::USERS_PASSWORD,
                'users_password_confirm' => self::USERS_PASSWORD,
            ]);

            fetch(Http::POST, (env('SERVER_URL') . '/api/auth/recovery/verify-code'), [
                'json' => [
                    'users_email' => fake()->email(),
                    'users_password_new' => $encode['users_password_new'],
                    'users_password_confirm' => $encode['users_password_confirm'],
                    'users_recovery_code' => fake()->numerify('######'),
                ],
            ]);
        });

        $this->assertJsonContent($this->getResponse($exception->getMessage(), 'response:'), [
            'code' => Http::UNAUTHORIZED,
            'status' => Status::SESSION_ERROR,
            'message' => 'email/password is incorrect [AUTH-1]',
        ]);
    }

    /**
     * @throws Exception
     */
    public function testUpdateLostPasswordInvalid1(): void
    {
        $response = fetch(Http::POST, (env('SERVER_URL') . '/api/auth/recovery/password'), [
            'json' => [
                'users_email' => self::USERS_EMAIL,
            ],
        ])
            ->getBody()
            ->getContents();

        $this->assertJsonContent($response, [
            'code' => Http::OK,
            'status' => Status::SUCCESS,
            'message' => 'confirmation code sent, check your email inbox to see your verification code',
        ]);

        $user = (new UsersModel())->readUsersByEmailDB(
            (new Users)
                ->setUsersEmail(self::USERS_EMAIL)
        );

        $this->assertIsObject($user);
        $this->assertObjectHasProperty('users_recovery_code', $user);

        $exception = $this->getExceptionFromApi(function () {
            $encode = $this->AESEncode([
                'users_password_new' => self::USERS_PASSWORD,
                'users_password_confirm' => self::USERS_PASSWORD,
            ]);

            fetch(Http::POST, (env('SERVER_URL') . '/api/auth/recovery/verify-code'), [
                'json' => [
                    'users_email' => self::USERS_EMAIL,
                    'users_password_new' => $encode['users_password_new'],
                    'users_password_confirm' => $encode['users_password_confirm'],
                    'users_recovery_code' => fake()->numerify('######'),
                ],
            ]);
        });

        $this->assertJsonContent($this->getResponse($exception->getMessage(), 'response:'), [
            'code' => Http::FORBIDDEN,
            'status' => Status::ERROR,
            'message' => 'verification code is invalid [ERR-2]',
        ]);
    }

    /**
     * @throws Exception
     */
    public function testUpdateLostPasswordIncorrect2(): void
    {
        $response = fetch(Http::POST, (env('SERVER_URL') . '/api/auth/recovery/password'), [
            'json' => [
                'users_email' => self::USERS_EMAIL,
            ],
        ])
            ->getBody()
            ->getContents();

        $this->assertJsonContent($response, [
            'code' => Http::OK,
            'status' => Status::SUCCESS,
            'message' => 'confirmation code sent, check your email inbox to see your verification code',
        ]);

        $user = (new UsersModel())->readUsersByEmailDB(
            (new Users)
                ->setUsersEmail(self::USERS_EMAIL)
        );

        $this->assertIsObject($user);
        $this->assertObjectHasProperty('users_recovery_code', $user);

        $exception = $this->getExceptionFromApi(function () use ($user) {
            $encode = $this->AESEncode([
                'users_password_new' => UsersFactory::USERS_PASSWORD,
                'users_password_confirm' => self::USERS_PASSWORD,
            ]);

            fetch(Http::POST, (env('SERVER_URL') . '/api/auth/recovery/verify-code'), [
                'json' => [
                    'users_email' => self::USERS_EMAIL,
                    'users_password_new' => $encode['users_password_new'],
                    'users_password_confirm' => $encode['users_password_confirm'],
                    'users_recovery_code' => $user->users_recovery_code,
                ],
            ]);
        });

        $this->assertJsonContent($this->getResponse($exception->getMessage(), 'response:'), [
            'code' => Http::UNAUTHORIZED,
            'status' => Status::ERROR,
            'message' => 'password is incorrect [ERR-2]',
        ]);
    }

    public function testUpdatePassword(): void
    {
        $users = (new UsersModel())->readUsersDB();

        $this->assertIsArray($users);

        $user = reset($users);

        $this->assertIsObject($user);
        $this->assertObjectHasProperty('idusers', $user);

        $encode = $this->AESEncode([
            'idusers' => (string) $user->idusers,
            'users_password' => UsersFactory::USERS_PASSWORD,
            'users_password_new' => self::USERS_PASSWORD,
            'users_password_confirm' => self::USERS_PASSWORD,
        ]);

        $response = fetch(Http::POST, (env('SERVER_URL') . '/api/profile/password'), [
            'headers' => [
                'Authorization' => $this->getAuthorization([
                    'idusers' => $encode['idusers'],
                ])
            ],
            'json' => [
                'users_password' => $encode['users_password'],
                'users_password_new' => $encode['users_password_new'],
                'users_password_confirm' => $encode['users_password_confirm'],
            ],
        ])
            ->getBody()
            ->getContents();

        $this->assertJsonContent($response, [
            'code' => Http::OK,
            'status' => Status::SUCCESS,
            'message' => 'password updated successfully',
        ]);
    }

    /**
     * @throws Exception
     */
    public function testUpdatePasswordIncorrect1(): void
    {
        $users = (new UsersModel())->readUsersDB();

        $this->assertIsArray($users);

        $user = reset($users);

        $this->assertIsObject($user);
        $this->assertObjectHasProperty('idusers', $user);

        $exception = $this->getExceptionFromApi(function () use ($user) {
            $encode = $this->AESEncode([
                'idusers' => (string) $user->idusers,
                'users_password' => self::USERS_PASSWORD,
                'users_password_new' => self::USERS_PASSWORD,
                'users_password_confirm' => self::USERS_PASSWORD,
            ]);

            fetch(Http::POST, (env('SERVER_URL') . '/api/profile/password'), [
                'headers' => [
                    'Authorization' => $this->getAuthorization([
                        'idusers' => $encode['idusers'],
                    ]),
                ],
                'json' => [
                    'users_password' => $encode['users_password'],
                    'users_password_new' => $encode['users_password_new'],
                    'users_password_confirm' => $encode['users_password_confirm'],
                ],
            ]);
        });

        $this->assertJsonContent($this->getResponse($exception->getMessage(), 'response:'), [
            'code' => Http::UNAUTHORIZED,
            'status' => Status::ERROR,
            'message' => 'password is incorrect [ERR-1]',
        ]);
    }

    /**
     * @throws Exception
     */
    public function testUpdatePasswordIncorrect2(): void
    {
        $users = (new UsersModel())->readUsersDB();

        $this->assertIsArray($users);

        $user = reset($users);

        $this->assertIsObject($user);
        $this->assertObjectHasProperty('idusers', $user);

        $exception = $this->getExceptionFromApi(function () use ($user) {
            $encode = $this->AESEncode([
                'idusers' => (string) $user->idusers,
                'users_password' => UsersFactory::USERS_PASSWORD,
                'users_password_new' => UsersFactory::USERS_PASSWORD,
                'users_password_confirm' => self::USERS_PASSWORD,
            ]);

            fetch(Http::POST, (env('SERVER_URL') . '/api/profile/password'), [
                'headers' => [
                    'Authorization' => $this->getAuthorization([
                        'idusers' => $encode['idusers'],
                    ]),
                ],
                'json' => [
                    'users_password' => $encode['users_password'],
                    'users_password_new' => $encode['users_password_new'],
                    'users_password_confirm' => $encode['users_password_confirm'],
                ],
            ]);
        });

        $this->assertJsonContent($this->getResponse($exception->getMessage(), 'response:'), [
            'code' => Http::UNAUTHORIZED,
            'status' => Status::ERROR,
            'message' => 'password is incorrect [ERR-2]',
        ]);
    }
}
