<?php

declare(strict_types=1);

namespace Tests\Api\LionDatabase\MySQL;

use App\Models\LionDatabase\MySQL\UsersModel;
use Database\Class\LionDatabase\MySQL\Users;
use Database\Factory\LionDatabase\MySQL\UsersFactory;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Lion\Database\Drivers\Schema\MySQL as Schema;
use Lion\Request\Http;
use Lion\Request\Status;
use Lion\Test\Test;
use PHPUnit\Framework\Attributes\Test as Testing;
use PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException;
use PragmaRX\Google2FA\Exceptions\InvalidCharactersException;
use PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException;
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

    /**
     * @throws GuzzleException
     */
    #[Testing]
    public function passwordVerify(): void
    {
        $user = $this->usersModel
            ->readUsersByEmailDB(
                (new Users())
                    ->setUsersEmail(UsersFactory::USERS_EMAIL)
            );

        $this->assertIsObject($user);
        $this->assertInstanceOf(stdClass::class, $user);
        $this->assertObjectHasProperty('idusers', $user);
        $this->assertIsInt($user->idusers);

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

    /**
     * @throws Exception
     */
    #[Testing]
    public function passwordVerifyPasswordIsInvalid(): void
    {
        $user = $this->usersModel
            ->readUsersByEmailDB(
                (new Users())
                    ->setUsersEmail(UsersFactory::USERS_EMAIL)
            );

        $this->assertIsObject($user);
        $this->assertInstanceOf(stdClass::class, $user);
        $this->assertObjectHasProperty('idusers', $user);
        $this->assertIsInt($user->idusers);

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

    /**
     * @throws GuzzleException
     */
    #[Testing]
    public function qr(): void
    {
        $user = $this->usersModel
            ->readUsersByEmailDB(
                (new Users())
                    ->setUsersEmail(UsersFactory::USERS_EMAIL)
            );

        $this->assertIsObject($user);
        $this->assertInstanceOf(stdClass::class, $user);
        $this->assertObjectHasProperty('idusers', $user);
        $this->assertIsInt($user->idusers);

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
        $this->assertInstanceOf(stdClass::class, $response);
        $this->assertObjectHasProperty('code', $response);
        $this->assertObjectHasProperty('status', $response);
        $this->assertObjectHasProperty('message', $response);
        $this->assertObjectHasProperty('data', $response);
        $this->assertObjectHasProperty('qr', $response->data);
        $this->assertObjectHasProperty('secret', $response->data);
        $this->assertIsInt($response->code);
        $this->assertSame(Http::OK, $response->code);
        $this->assertIsString($response->status);
        $this->assertSame(Status::SUCCESS, $response->status);
        $this->assertNull($response->message);
        $this->assertIsString($response->data->qr);
        $this->assertIsString($response->data->secret);
    }

    /**
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws InvalidCharactersException
     * @throws GuzzleException
     * @throws SecretKeyTooShortException
     */
    #[Testing]
    public function enable2FA(): void
    {
        $user = $this->usersModel
            ->readUsersByEmailDB(
                (new Users())
                    ->setUsersEmail(UsersFactory::USERS_EMAIL)
            );

        $this->assertIsObject($user);
        $this->assertInstanceOf(stdClass::class, $user);
        $this->assertObjectHasProperty('idusers', $user);
        $this->assertIsInt($user->idusers);

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
        $this->assertObjectHasProperty('code', $response);
        $this->assertObjectHasProperty('status', $response);
        $this->assertObjectHasProperty('message', $response);
        $this->assertIsInt($response->code);
        $this->assertSame(Http::OK, $response->code);
        $this->assertIsString($response->status);
        $this->assertSame(Status::SUCCESS, $response->status);
        $this->assertIsString($response->message);
        $this->assertSame('2FA authentication has been enabled', $response->message);
    }

    /**
     * @throws Exception
     */
    #[Testing]
    public function enable2FACheckStatusIsActive(): void
    {
        $user = $this->usersModel
            ->readUsersByEmailDB(
                (new Users())
                    ->setUsersEmail(UsersFactory::USERS_EMAIL_SECURITY)
            );

        $this->assertIsObject($user);
        $this->assertInstanceOf(stdClass::class, $user);
        $this->assertObjectHasProperty('idusers', $user);
        $this->assertIsInt($user->idusers);

        $aesEncode = $this->AESEncode([
            'idusers' => (string) $user->idusers,
            'users_2fa_secret' => UsersFactory::SECURITY_KEY_2FA,
        ]);

        $exception = $this->getExceptionFromApi(function () use ($aesEncode): void {
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
            ]);
        });

        $this->assertJsonContent($this->getResponse($exception->getMessage(), 'response:'), [
            'code' => Http::INTERNAL_SERVER_ERROR,
            'status' => Status::ERROR,
            'message' => '2FA security is active',
        ]);
    }

    /**
     * @throws Exception
     */
    #[Testing]
    public function enable2FAVerify2FAIsError(): void
    {
        $user = $this->usersModel
            ->readUsersByEmailDB(
                (new Users())
                    ->setUsersEmail(UsersFactory::USERS_EMAIL)
            );

        $this->assertIsObject($user);
        $this->assertInstanceOf(stdClass::class, $user);
        $this->assertObjectHasProperty('idusers', $user);
        $this->assertIsInt($user->idusers);

        $aesEncode = $this->AESEncode([
            'idusers' => (string) $user->idusers,
            'users_2fa_secret' => UsersFactory::SECURITY_KEY_2FA,
        ]);

        $exception = $this->getExceptionFromApi(function () use ($aesEncode): void {
            fetch(Http::POST, (env('SERVER_URL') . '/api/profile/2fa/enable'), [
                'headers' => [
                    'Authorization' => $this->getAuthorization([
                        'idusers' => $aesEncode['idusers'],
                    ]),
                ],
                'json' => [
                    'users_2fa_secret' => $aesEncode['users_2fa_secret'],
                    'users_secret_code' => '000000',
                ],
            ]);
        });

        $this->assertJsonContent($this->getResponse($exception->getMessage(), 'response:'), [
            'code' => Http::FORBIDDEN,
            'status' => Status::ERROR,
            'message' => 'failed to authenticate, the code is not valid',
        ]);
    }

    /**
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws InvalidCharactersException
     * @throws GuzzleException
     * @throws SecretKeyTooShortException
     */
    #[Testing]
    public function disable2FA(): void
    {
        $user = $this->usersModel
            ->readUsersByEmailDB(
                (new Users())
                    ->setUsersEmail(UsersFactory::USERS_EMAIL_SECURITY)
            );

        $this->assertIsObject($user);
        $this->assertInstanceOf(stdClass::class, $user);
        $this->assertObjectHasProperty('idusers', $user);
        $this->assertIsInt($user->idusers);

        $aesEncode = $this->AESEncode([
            'idusers' => (string) $user->idusers,
        ]);

        $response = json_decode(
            fetch(Http::POST, (env('SERVER_URL') . '/api/profile/2fa/disable'), [
                'headers' => [
                    'Authorization' => $this->getAuthorization([
                        'idusers' => $aesEncode['idusers'],
                    ]),
                ],
                'json' => [
                    'users_secret_code' => (new Google2FA())
                        ->getCurrentOtp(UsersFactory::SECURITY_KEY_2FA),
                ],
            ])
                ->getBody()
                ->getContents()
        );

        $this->assertIsObject($response);
        $this->assertInstanceOf(stdClass::class, $response);
        $this->assertObjectHasProperty('code', $response);
        $this->assertObjectHasProperty('status', $response);
        $this->assertObjectHasProperty('message', $response);
        $this->assertIsInt($response->code);
        $this->assertSame(Http::OK, $response->code);
        $this->assertIsString($response->status);
        $this->assertSame(Status::SUCCESS, $response->status);
        $this->assertIsString($response->message);
        $this->assertSame('2FA authentication has been disabled', $response->message);
    }

    /**
     * @throws Exception
     */
    #[Testing]
    public function disable2FACheckStatusIsInactive(): void
    {
        $user = $this->usersModel
            ->readUsersByEmailDB(
                (new Users())
                    ->setUsersEmail(UsersFactory::USERS_EMAIL)
            );

        $this->assertIsObject($user);
        $this->assertInstanceOf(stdClass::class, $user);
        $this->assertObjectHasProperty('idusers', $user);
        $this->assertIsInt($user->idusers);

        $aesEncode = $this->AESEncode([
            'idusers' => (string) $user->idusers,
        ]);

        $exception = $this->getExceptionFromApi(function () use ($aesEncode): void {
            fetch(Http::POST, (env('SERVER_URL') . '/api/profile/2fa/disable'), [
                'headers' => [
                    'Authorization' => $this->getAuthorization([
                        'idusers' => $aesEncode['idusers'],
                    ]),
                ],
                'json' => [
                    'users_secret_code' => (new Google2FA())
                        ->getCurrentOtp(UsersFactory::SECURITY_KEY_2FA),
                ],
            ]);
        });

        $this->assertJsonContent($this->getResponse($exception->getMessage(), 'response:'), [
            'code' => Http::INTERNAL_SERVER_ERROR,
            'status' => Status::ERROR,
            'message' => '2FA security is inactive',
        ]);
    }

    /**
     * @throws Exception
     */
    #[Testing]
    public function disable2FAVerify2FAIsError(): void
    {
        $user = $this->usersModel
            ->readUsersByEmailDB(
                (new Users())
                    ->setUsersEmail(UsersFactory::USERS_EMAIL_SECURITY)
            );

        $this->assertIsObject($user);
        $this->assertInstanceOf(stdClass::class, $user);
        $this->assertObjectHasProperty('idusers', $user);
        $this->assertIsInt($user->idusers);

        $aesEncode = $this->AESEncode([
            'idusers' => (string) $user->idusers,
        ]);

        $exception = $this->getExceptionFromApi(function () use ($aesEncode): void {
            fetch(Http::POST, (env('SERVER_URL') . '/api/profile/2fa/disable'), [
                'headers' => [
                    'Authorization' => $this->getAuthorization([
                        'idusers' => $aesEncode['idusers'],
                    ]),
                ],
                'json' => [
                    'users_secret_code' => '000000',
                ],
            ]);
        });

        $this->assertJsonContent($this->getResponse($exception->getMessage(), 'response:'), [
            'code' => Http::FORBIDDEN,
            'status' => Status::ERROR,
            'message' => 'failed to authenticate, the code is not valid',
        ]);
    }
}
