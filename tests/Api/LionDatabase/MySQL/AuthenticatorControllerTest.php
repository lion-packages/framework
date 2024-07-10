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
use PragmaRX\Google2FAQRCode\Google2FA;
use stdClass;
use Tests\Providers\AuthJwtProviderTrait;
use Tests\Providers\SetUpMigrationsAndQueuesProviderTrait;

class AuthenticatorControllerTest extends Test
{
    use AuthJwtProviderTrait;
    use SetUpMigrationsAndQueuesProviderTrait;

    private UsersModel $usersModel;

    protected function setUp(): void
    {
        $this->runMigrationsAndQueues();

        $this->usersModel = new UsersModel();
    }

    protected function tearDown(): void
    {
        Schema::truncateTable('users')->execute();
    }

    #[Testing]
    public function passwordVerify(): void
    {
        $users = $this->usersModel->readUsersDB();

        $this->assertIsArray($users);
        $this->assertCount(self::AVAILABLE_USERS, $users);

        $user = reset($users);

        $this->assertIsObject($user);
        $this->assertObjectHasProperty('idusers', $user);

        $aesEncode = $this->AESEncode([
            'idusers' => (string) $user->idusers,
            'users_password' => UsersFactory::USERS_PASSWORD,
        ]);

        $response = fetch(Http::POST, (env('SERVER_URL') . '/api/profile/2fa/verify'), [
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
        $users = $this->usersModel->readUsersDB();

        $this->assertIsArray($users);
        $this->assertCount(self::AVAILABLE_USERS, $users);

        $user = reset($users);

        $this->assertIsObject($user);
        $this->assertObjectHasProperty('idusers', $user);

        $aesEncode = $this->AESEncode([
            'idusers' => (string) $user->idusers,
            'users_password' => fake()->numerify('#########'),
        ]);

        $exception = $this->getExceptionFromApi(function () use ($aesEncode): void {
            fetch(Http::POST, (env('SERVER_URL') . '/api/profile/2fa/verify'), [
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
        $users = $this->usersModel->readUsersDB();

        $this->assertIsArray($users);
        $this->assertCount(self::AVAILABLE_USERS, $users);

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

    #[Testing]
    public function enable2FA(): void
    {
        $users = $this->usersModel->readUsersDB();

        $this->assertIsArray($users);
        $this->assertCount(self::AVAILABLE_USERS, $users);

        $user = reset($users);

        $this->assertIsObject($user);
        $this->assertObjectHasProperty('idusers', $user);

        $aesEncode = $this->AESEncode([
            'idusers' => (string) $user->idusers,
            'users_2fa_secret' => UsersFactory::SECURITY_KEY_2FA,
        ]);

        $response = json_decode(
            fetch(Http::POST, (env('SERVER_URL') . '/api/profile/2fa/enable'), [
                'headers' => [
                    'Authorization' => $this->getAuthorization([
                        'idusers' => $aesEncode['idusers'],
                    ]),
                ],
                'json' => [
                    'users_2fa_secret' => $aesEncode['users_2fa_secret'],
                    'users_secret_code' => (new Google2FA())
                        ->getCurrentOtp(UsersFactory::SECURITY_KEY_2FA),
                ],
            ])
                ->getBody()
                ->getContents()
        );

        $this->assertIsObject($response);
        $this->assertInstanceOf(stdClass::class, $response);
    }
}
