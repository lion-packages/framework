<?php

declare(strict_types=1);

namespace Tests\App\Http\Controllers\LionDatabase\MySQL;

use App\Models\LionDatabase\MySQL\UsersModel;
use Database\Factory\LionDatabase\MySQL\UsersFactory;
use Lion\Request\Request;
use Lion\Request\Response;
use Lion\Route\Route;
use Lion\Security\Validation;
use Lion\Test\Test;
use Tests\Providers\AuthJwtProviderTrait;
use Tests\Providers\SetUpMigrationsAndQueuesProviderTrait;

class PasswordManagerControllerTest extends Test
{
    use AuthJwtProviderTrait;
    use SetUpMigrationsAndQueuesProviderTrait;

    const USERS_EMAIL = 'root@dev.com';
    const USERS_PASSWORD = 'lion-password';

    protected function setUp(): void
    {
        $this->runMigrationsAndQueues();
    }

    public function testRecoveryPassword(): void
    {
        $response = fetch(Route::POST, (env('SERVER_URL') . '/api/auth/password/recovery'), [
            'json' => [
                'users_email' => self::USERS_EMAIL
            ]
        ])
            ->getBody()
            ->getContents();

        $this->assertJsonContent($response, [
            'code' => Request::HTTP_OK,
            'status' => Response::SUCCESS,
            'message' => 'confirmation code sent, check your email inbox to see your verification code',
        ]);
    }

    public function testRecoveryPasswordIncorrect1(): void
    {
        $exception = $this->getExceptionFromApi(function () {
            fetch(Route::POST, (env('SERVER_URL') . '/api/auth/password/recovery'), [
                'json' => [
                    'users_email' => fake()->email()
                ]
            ]);
        });

        $this->assertJsonContent($this->getResponse($exception->getMessage(), 'response:'), [
            'code' => Request::HTTP_UNAUTHORIZED,
            'status' => Response::SESSION_ERROR,
            'message' => 'email/password is incorrect [AUTH-1]',
        ]);
    }

    public function testUpdatePassword(): void
    {
        $users = (new UsersModel())->readUsersDB();

        $this->assertIsArray($users);

        $user = reset($users);

        $this->assertIsObject($user);
        $this->assertObjectHasProperty('idusers', $user);

        $validation = new Validation();

        $response = fetch(Route::POST, (env('SERVER_URL') . '/api/auth/password/update'), [
            'headers' => [
                'Authorization' => $this->getAuthorization(['idusers' => $user->idusers])
            ],
            'json' => [
                'users_password' => $validation->sha256(UsersFactory::USERS_PASSWORD),
                'users_password_new' => $validation->sha256(self::USERS_PASSWORD),
                'users_password_confirm' => $validation->sha256(self::USERS_PASSWORD),
            ]
        ])
            ->getBody()
            ->getContents();

        $this->assertJsonContent($response, [
            'code' => Request::HTTP_OK,
            'status' => Response::SUCCESS,
            'message' => 'password updated successfully',
        ]);
    }

    public function testUpdatePasswordIncorrect1(): void
    {
        $users = (new UsersModel())->readUsersDB();

        $this->assertIsArray($users);

        $user = reset($users);

        $this->assertIsObject($user);
        $this->assertObjectHasProperty('idusers', $user);

        $exception = $this->getExceptionFromApi(function () use ($user) {
            $validation = new Validation();

            fetch(Route::POST, (env('SERVER_URL') . '/api/auth/password/update'), [
                'headers' => [
                    'Authorization' => $this->getAuthorization(['idusers' => $user->idusers])
                ],
                'json' => [
                    'users_password' => $validation->sha256(self::USERS_PASSWORD),
                    'users_password_new' => $validation->sha256(self::USERS_PASSWORD),
                    'users_password_confirm' => $validation->sha256(self::USERS_PASSWORD),
                ]
            ]);
        });

        $this->assertJsonContent($this->getResponse($exception->getMessage(), 'response:'), [
            'code' => Request::HTTP_UNAUTHORIZED,
            'status' => Response::ERROR,
            'message' => 'password is incorrect [ERR-1]',
        ]);
    }

    public function testUpdatePasswordIncorrect2(): void
    {
        $users = (new UsersModel())->readUsersDB();

        $this->assertIsArray($users);

        $user = reset($users);

        $this->assertIsObject($user);
        $this->assertObjectHasProperty('idusers', $user);

        $exception = $this->getExceptionFromApi(function () use ($user) {
            $validation = new Validation();

            fetch(Route::POST, (env('SERVER_URL') . '/api/auth/password/update'), [
                'headers' => [
                    'Authorization' => $this->getAuthorization(['idusers' => $user->idusers])
                ],
                'json' => [
                    'users_password' => $validation->sha256(UsersFactory::USERS_PASSWORD),
                    'users_password_new' => $validation->sha256(UsersFactory::USERS_PASSWORD),
                    'users_password_confirm' => $validation->sha256(self::USERS_PASSWORD),
                ]
            ]);
        });

        $this->assertJsonContent($this->getResponse($exception->getMessage(), 'response:'), [
            'code' => Request::HTTP_UNAUTHORIZED,
            'status' => Response::ERROR,
            'message' => 'password is incorrect [ERR-2]',
        ]);
    }
}
