<?php

declare(strict_types=1);

namespace Tests\Api\LionDatabase\MySQL;

use App\Enums\RolesEnum;
use Database\Factory\LionDatabase\MySQL\UsersFactory;
use Database\Migrations\LionDatabase\MySQL\Tables\DocumentTypes as DocumentTypesTable;
use Database\Migrations\LionDatabase\MySQL\Tables\Roles as RolesTable;
use Database\Migrations\LionDatabase\MySQL\Tables\Users as UsersTable;
use Database\Migrations\LionDatabase\MySQL\Views\ReadUsersById;
use Database\Seed\LionDatabase\MySQL\DocumentTypesSeed;
use Database\Seed\LionDatabase\MySQL\RolesSeed;
use Database\Seed\LionDatabase\MySQL\UsersSeed;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Lion\Bundle\Test\Test;
use Lion\Request\Http;
use Lion\Request\Status;
use PHPUnit\Framework\Attributes\Test as Testing;
use PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException;
use PragmaRX\Google2FA\Exceptions\InvalidCharactersException;
use PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException;
use PragmaRX\Google2FAQRCode\Google2FA;
use stdClass;
use Tests\Providers\AuthJwtProviderTrait;

class LoginControllerTest extends Test
{
    use AuthJwtProviderTrait;

    private const string USERS_EMAIL_MANAGER = 'manager@dev.com';

    protected function setUp(): void
    {
        $this->executeMigrationsGroup([
            DocumentTypesTable::class,
            RolesTable::class,
            UsersTable::class,
            ReadUsersById::class,
        ]);

        $this->executeSeedsGroup([
            DocumentTypesSeed::class,
            RolesSeed::class,
            UsersSeed::class,
        ]);
    }

    /**
     * @throws GuzzleException
     */
    #[Testing]
    public function auth(): void
    {
        $encode = $this->AESEncode([
            'users_password' => UsersFactory::USERS_PASSWORD,
        ]);

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
        $this->assertInstanceOf(stdClass::class, $response);
        $this->assertObjectHasProperty('code', $response);
        $this->assertObjectHasProperty('status', $response);
        $this->assertObjectHasProperty('message', $response);
        $this->assertObjectHasProperty('data', $response);
        $this->assertObjectHasProperty('full_name', $response->data);
        $this->assertObjectHasProperty('jwt_access', $response->data);
        $this->assertObjectHasProperty('jwt_refresh', $response->data);
        $this->assertIsInt($response->code);
        $this->assertIsString($response->status);
        $this->assertIsString($response->message);
        $this->assertIsObject($response->data);
        $this->assertInstanceOf(stdClass::class, $response->data);
        $this->assertIsString($response->data->full_name);
        $this->assertIsString($response->data->jwt_access);
        $this->assertIsString($response->data->jwt_refresh);
        $this->assertSame(Http::OK, $response->code);
        $this->assertSame(Status::SUCCESS, $response->status);
        $this->assertSame('successfully authenticated user', $response->message);
    }

    /**
     * @throws Exception
     */
    #[Testing]
    public function authIncorrect1(): void
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
    #[Testing]
    public function authIncorrect2(): void
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
    #[Testing]
    public function authVerifyAccount(): void
    {
        $exception = $this->getExceptionFromApi(function () {
            $encode = $this->AESEncode([
                'users_password' => UsersFactory::USERS_PASSWORD,
            ]);

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

    /**
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws InvalidCharactersException
     * @throws GuzzleException
     * @throws SecretKeyTooShortException
     */
    #[Testing]
    public function auth2FA(): void
    {
        $response = json_decode(
            fetch(Http::POST, (env('SERVER_URL') . '/api/auth/2fa'), [
                'json' => [
                    'users_email' => UsersFactory::USERS_EMAIL_SECURITY,
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
        $this->assertObjectHasProperty('data', $response);
        $this->assertObjectHasProperty('full_name', $response->data);
        $this->assertObjectHasProperty('jwt_access', $response->data);
        $this->assertObjectHasProperty('jwt_refresh', $response->data);
        $this->assertIsInt($response->code);
        $this->assertIsString($response->status);
        $this->assertIsString($response->message);
        $this->assertIsString($response->data->full_name);
        $this->assertIsString($response->data->jwt_access);
        $this->assertIsString($response->data->jwt_refresh);
        $this->assertSame(Http::OK, $response->code);
        $this->assertSame(Status::SUCCESS, $response->status);
        $this->assertSame('successfully authenticated user', $response->message);
    }

    /**
     * @throws Exception
     */
    #[Testing]
    public function auth2FAIsError(): void
    {
        $exception = $this->getExceptionFromApi(function (): void {
            fetch(Http::POST, (env('SERVER_URL') . '/api/auth/2fa'), [
                'json' => [
                    'users_email' => UsersFactory::USERS_EMAIL,
                    'users_secret_code' => (new Google2FA())
                        ->getCurrentOtp(UsersFactory::SECURITY_KEY_2FA),
                ],
            ]);
        });

        $this->assertJsonContent($this->getResponse($exception->getMessage(), 'response:'), [
            'code' => Http::FORBIDDEN,
            'status' => Status::ERROR,
            'message' => '2FA security is not active for this user',
        ]);
    }

    /**
     * @throws Exception
     */
    #[Testing]
    public function auth2FAVerify2FAIsError(): void
    {
        $exception = $this->getExceptionFromApi(function (): void {
            fetch(Http::POST, (env('SERVER_URL') . '/api/auth/2fa'), [
                'json' => [
                    'users_email' => UsersFactory::USERS_EMAIL_SECURITY,
                    'users_secret_code' => fake()->numerify('######'),
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
     * @throws GuzzleException
     */
    #[Testing]
    public function refresh(): void
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
        $this->assertInstanceOf(stdClass::class, $response);
        $this->assertObjectHasProperty('code', $response);
        $this->assertObjectHasProperty('status', $response);
        $this->assertObjectHasProperty('message', $response);
        $this->assertObjectHasProperty('data', $response);
        $this->assertObjectHasProperty('jwt_access', $response->data);
        $this->assertObjectHasProperty('jwt_refresh', $response->data);
        $this->assertObjectHasProperty('auth_2fa', $response->data);
        $this->assertIsInt($response->code);
        $this->assertIsString($response->status);
        $this->assertIsString($response->message);
        $this->assertIsObject($response->data);
        $this->assertInstanceOf(stdClass::class, $response->data);
        $this->assertIsString($response->data->jwt_access);
        $this->assertIsString($response->data->jwt_refresh);
        $this->assertIsBool($response->data->auth_2fa);
        $this->assertSame(Http::OK, $response->code);
        $this->assertSame(Status::SUCCESS, $response->status);
        $this->assertSame('successfully authenticated user', $response->message);
    }
}
