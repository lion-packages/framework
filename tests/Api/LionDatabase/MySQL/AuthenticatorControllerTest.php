<?php

declare(strict_types=1);

namespace Tests\Api\LionDatabase\MySQL;

use App\Models\LionDatabase\MySQL\UsersModel;
use Database\Factory\LionDatabase\MySQL\UsersFactory;
use Lion\Database\Drivers\Schema\MySQL as Schema;
use Lion\Request\Http;
use Lion\Request\Status;
use Lion\Test\Test;
use PHPUnit\Framework\Attributes\Test as Testing;
use Tests\Providers\AuthJwtProviderTrait;
use Tests\Providers\SetUpMigrationsAndQueuesProviderTrait;

class AuthenticatorControllerTest extends Test
{
    use AuthJwtProviderTrait;
    use SetUpMigrationsAndQueuesProviderTrait;

    protected function setUp(): void
    {
        $this->runMigrationsAndQueues();
    }

    protected function tearDown(): void
    {
        Schema::truncateTable('users')->execute();
    }

    #[Testing]
    public function verifyPassword(): void
    {
        $users = (new UsersModel())->readUsersDB();

        $this->assertIsArray($users);
        $this->assertCount(2, $users);

        $user = reset($users);

        $this->assertIsObject($user);
        $this->assertObjectHasProperty('idusers', $user);

        $aesEncode = $this->AESEncode([
            'idusers' => (string) $user->idusers,
            'users_password' => UsersFactory::USERS_PASSWORD,
        ]);

        $response = fetch(Http::POST, (env('SERVER_URL') . '/api/profile/password/verify'), [
            'headers' => [
                'Authorization' => $this->getAuthorization([
                    'idusers' => $aesEncode['idusers'],
                ]),
            ],
            'json' => [
                'users_password' => $aesEncode['users_password'],
            ],
        ])
            ->getBody()
            ->getContents();

        $this->assertJsonContent($response, [
            'code' => Http::OK,
            'status' => Status::SUCCESS,
            'message' => 'the password is valid',
        ]);
    }

    #[Testing]
    public function passwordVerifyPasswordIsInvalid(): void
    {
        $users = (new UsersModel())->readUsersDB();

        $this->assertIsArray($users);
        $this->assertCount(2, $users);

        $user = reset($users);

        $this->assertIsObject($user);
        $this->assertObjectHasProperty('idusers', $user);

        $aesEncode = $this->AESEncode([
            'idusers' => (string) $user->idusers,
            'users_password' => fake()->numerify('#########'),
        ]);

        $exception = $this->getExceptionFromApi(function () use ($aesEncode): void {
            fetch(Http::POST, (env('SERVER_URL') . '/api/profile/password/verify'), [
                'headers' => [
                    'Authorization' => $this->getAuthorization([
                        'idusers' => $aesEncode['idusers'],
                    ]),
                ],
                'json' => [
                    'users_password' => $aesEncode['users_password'],
                ],
            ]);
        });

        $this->assertJsonContent($this->getResponse($exception->getMessage(), 'response:'), [
            'code' => Http::INTERNAL_SERVER_ERROR,
            'status' => Status::ERROR,
            'message' => 'password is invalid',
        ]);
    }

    #[Testing]
    public function qr(): void
    {
        $users = (new UsersModel())->readUsersDB();

        $this->assertIsArray($users);
        $this->assertCount(2, $users);

        $user = reset($users);

        $this->assertIsObject($user);
        $this->assertObjectHasProperty('idusers', $user);

        $response = json_decode(
            fetch(Http::GET, (env('SERVER_URL') . '/api/profile/2fa/qr'), [
                'headers' => [
                    'Authorization' => $this->getAuthorization($this->AESEncode([
                        'idusers' => (string) $user->idusers,
                    ])),
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
        $this->assertObjectHasProperty('qr', $response->data);
        $this->assertObjectHasProperty('secret', $response->data);
        $this->assertSame(Http::OK, $response->code);
        $this->assertSame(Status::SUCCESS, $response->status);
        $this->assertNull($response->message);
        $this->assertIsString($response->data->qr);
        $this->assertIsString($response->data->secret);
    }
}
