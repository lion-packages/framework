<?php

declare(strict_types=1);

namespace Tests\Api\LionDatabase\MySQL;

use App\Enums\RolesEnum;
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
        $this->assertObjectHasProperty('full_name', $response->data);
        $this->assertObjectHasProperty('jwt_access', $response->data);
        $this->assertObjectHasProperty('jwt_refresh', $response->data);
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
            'message' => 'email/password is incorrect [AUTH-1]',
        ]);
    }

    /**
     * @throws Exception
     */
    public function testAuthIncorrect2(): void
    {
        $exception = $this->getExceptionFromApi(function (): void {
            $encode = $this->AESEncode([
                'users_password' => UsersFactory::USERS_PASSWORD . '-x',
            ]);

            fetch(Http::POST, (env('SERVER_URL') . '/api/auth/login'), [
                'json' => [
                    'users_email' => UsersFactory::USERS_EMAIL,
                    'users_password' => $encode['users_password'],
                ],
            ]);
        });

        $this->assertJsonContent($this->getResponse($exception->getMessage(), 'response:'), [
            'code' => Http::UNAUTHORIZED,
            'status' => Status::ERROR,
            'message' => 'email/password is incorrect [AUTH-2]',
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

    public function testRefresh(): void
    {
        $encode = $this->AESEncode([
            'idusers' => "1",
            'idroles' => (string) RolesEnum::ADMINISTRATOR->value,
        ]);

        $jwtEncode = $this->AESEncode([
            'jwt_refresh' => str->of(
                $this->getAuthorization([
                    'session' => true,
                    'idusers' => $encode['idusers'],
                    'idroles' => $encode['idroles'],
                ])
            )
                ->replace('Bearer', '')
                ->trim()
                ->get(),
        ]);

        $response = json_decode(
            fetch(Http::POST, (env('SERVER_URL') . '/api/auth/refresh'), [
                'headers' => [
                    'Authorization' => $this->getAuthorization([
                        'idusers' => $encode['idusers'],
                        'idroles' => $encode['idroles'],
                    ]),
                ],
                'json' => [
                    'jwt_refresh' => $jwtEncode['jwt_refresh'],
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
        $this->assertSame(Http::OK, $response->code);
        $this->assertSame(Status::SUCCESS, $response->status);
        $this->assertSame('successfully authenticated user', $response->message);
        $this->assertIsObject($response->data);
        $this->assertObjectHasProperty('jwt_access', $response->data);
        $this->assertObjectHasProperty('jwt_refresh', $response->data);
        $this->assertIsString($response->data->jwt_access);
        $this->assertIsString($response->data->jwt_refresh);
    }
}
