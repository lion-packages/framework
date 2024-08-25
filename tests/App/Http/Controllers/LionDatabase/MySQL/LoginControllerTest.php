<?php

declare(strict_types=1);

namespace Tests\App\Http\Controllers\LionDatabase\MySQL;

use App\Enums\RolesEnum;
use App\Exceptions\AuthenticationException;
use App\Exceptions\PasswordException;
use App\Exceptions\ProcessException;
use App\Http\Controllers\LionDatabase\MySQL\LoginController;
use App\Http\Services\AESService;
use App\Http\Services\JWTService;
use App\Http\Services\LionDatabase\MySQL\AuthenticatorService;
use App\Http\Services\LionDatabase\MySQL\LoginService;
use App\Http\Services\LionDatabase\MySQL\PasswordManagerService;
use App\Models\LionDatabase\MySQL\AuthenticatorModel;
use App\Models\LionDatabase\MySQL\LoginModel;
use App\Models\LionDatabase\MySQL\UsersModel;
use Database\Class\Authenticator2FA;
use Database\Class\LionDatabase\MySQL\Users;
use Database\Factory\LionDatabase\MySQL\UsersFactory;
use Lion\Authentication\Auth2FA;
use Lion\Request\Http;
use Lion\Request\Status;
use Lion\Security\AES;
use Lion\Security\JWT;
use Lion\Security\RSA;
use Lion\Security\Validation;
use PHPUnit\Framework\Attributes\Test as Testing;
use PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException;
use PragmaRX\Google2FA\Exceptions\InvalidCharactersException;
use PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException;
use PragmaRX\Google2FAQRCode\Google2FA;
use stdClass;
use Tests\Providers\AuthJwtProviderTrait;
use Tests\Providers\SetUpMigrationsAndQueuesProviderTrait;
use Tests\Test;

class LoginControllerTest extends Test
{
    use AuthJwtProviderTrait;
    use SetUpMigrationsAndQueuesProviderTrait;

    private LoginController $loginController;
    private UsersModel $usersModel;

    protected function setUp(): void
    {
        $this->runMigrations();

        $this->loginController = new LoginController();

        $this->usersModel = new UsersModel();
    }

    /**
     * @throws PasswordException
     * @throws AuthenticationException
     */
    #[Testing]
    public function auth(): void
    {
        $encode = $this->AESEncode([
            'users_password' => UsersFactory::USERS_PASSWORD,
        ]);

        $_POST['users_email'] = UsersFactory::USERS_EMAIL;

        $_POST['users_password'] = $encode['users_password'];

        $response = $this->loginController->auth(
            new Users(),
            new Authenticator2FA(),
            new LoginModel(),
            (new LoginService())
                ->setJWT(new JWT())
                ->setRSA(new RSA())
                ->setLoginModel(new LoginModel())
                ->setAuthenticatorModel(new AuthenticatorModel())
                ->setAESService(
                    (new AESService())
                        ->setAES(new AES())
                )
                ->setJWTService(
                    (new JWTService())
                        ->setJWT(new JWT())
                        ->setRSA(new RSA())
                ),
            (new PasswordManagerService())
                ->setValidation(new Validation()),
            (new AESService())
                ->setAES(new AES())
        );

        $this->assertIsObject($response);
        $this->assertInstanceOf(stdClass::class, $response);
        $this->assertObjectHasProperty('code', $response);
        $this->assertObjectHasProperty('status', $response);
        $this->assertObjectHasProperty('message', $response);
        $this->assertObjectHasProperty('data', $response);
        $this->assertArrayHasKey('auth_2fa', $response->data);
        $this->assertArrayHasKey('full_name', $response->data);
        $this->assertArrayHasKey('jwt_access', $response->data);
        $this->assertArrayHasKey('jwt_refresh', $response->data);
        $this->assertIsInt($response->code);
        $this->assertIsString($response->status);
        $this->assertIsString($response->message);
        $this->assertIsArray($response->data);
        $this->assertIsBool($response->data['auth_2fa']);
        $this->assertIsString($response->data['full_name']);
        $this->assertIsString($response->data['jwt_access']);
        $this->assertIsString($response->data['jwt_refresh']);
        $this->assertSame(Http::OK, $response->code);
        $this->assertSame(Status::SUCCESS, $response->status);
        $this->assertSame('successfully authenticated user', $response->message);
        $this->assertArrayNotHasKeyFromList($_POST, ['users_email', 'users_password']);
    }

    /**
     * @throws PasswordException
     * @throws AuthenticationException
     */
    #[Testing]
    public function authIsWarning(): void
    {
        $encode = $this->AESEncode([
            'users_password' => UsersFactory::USERS_PASSWORD,
        ]);

        $_POST['users_email'] = UsersFactory::USERS_EMAIL_SECURITY;

        $_POST['users_password'] = $encode['users_password'];

        $response = $this->loginController->auth(
            new Users(),
            new Authenticator2FA(),
            new LoginModel(),
            (new LoginService())
                ->setJWT(new JWT())
                ->setRSA(new RSA())
                ->setLoginModel(new LoginModel())
                ->setAuthenticatorModel(new AuthenticatorModel())
                ->setAESService(
                    (new AESService())
                        ->setAES(new AES())
                )
                ->setJWTService(
                    (new JWTService())
                        ->setJWT(new JWT())
                        ->setRSA(new RSA())
                ),
            (new PasswordManagerService())
                ->setValidation(new Validation()),
            (new AESService())
                ->setAES(new AES())
        );

        $this->assertIsObject($response);
        $this->assertInstanceOf(stdClass::class, $response);
        $this->assertObjectHasProperty('code', $response);
        $this->assertObjectHasProperty('status', $response);
        $this->assertObjectHasProperty('message', $response);
        $this->assertIsInt($response->code);
        $this->assertIsString($response->status);
        $this->assertNull($response->message);
        $this->assertSame(Http::ACCEPTED, $response->code);
        $this->assertSame(Status::WARNING, $response->status);
        $this->assertNull($response->message);
        $this->assertArrayNotHasKeyFromList($_POST, ['users_email', 'users_password']);
    }

    /**
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws ProcessException
     * @throws InvalidCharactersException
     * @throws SecretKeyTooShortException
     */
    #[Testing]
    public function auth2FA(): void
    {
        /** @var stdClass $user */
        $user = $this->usersModel->readUsersByEmailDB(
            (new Users())
                ->setUsersEmail(UsersFactory::USERS_EMAIL_SECURITY)
        );

        $_POST['users_email'] = UsersFactory::USERS_EMAIL_SECURITY;

        $_POST['users_secret_code'] = (new Google2FA())->getCurrentOtp($user->users_2fa_secret);

        $response = $this->loginController->auth2FA(
            new Users(),
            new Authenticator2FA(),
            new LoginModel(),
            (new LoginService())
                ->setJWT(new JWT())
                ->setRSA(new RSA())
                ->setLoginModel(new LoginModel())
                ->setAuthenticatorModel(new AuthenticatorModel())
                ->setAESService(
                    (new AESService())
                        ->setAES(new AES())
                )
                ->setJWTService(
                    (new JWTService())
                        ->setJWT(new JWT())
                        ->setRSA(new RSA())
                ),
            (new AuthenticatorService())
                ->setAuthenticatorModel(new AuthenticatorModel())
                ->setAuth2FA(new Auth2FA())
            );

        $this->assertIsObject($response);
        $this->assertInstanceOf(stdClass::class, $response);
        $this->assertObjectHasProperty('code', $response);
        $this->assertObjectHasProperty('status', $response);
        $this->assertObjectHasProperty('message', $response);
        $this->assertObjectHasProperty('data', $response);
        $this->assertArrayHasKey('full_name', $response->data);
        $this->assertArrayHasKey('jwt_access', $response->data);
        $this->assertArrayHasKey('jwt_refresh', $response->data);
        $this->assertIsInt($response->code);
        $this->assertIsString($response->status);
        $this->assertIsString($response->message);
        $this->assertIsArray($response->data);
        $this->assertIsBool($response->data['auth_2fa']);
        $this->assertIsString($response->data['full_name']);
        $this->assertIsString($response->data['jwt_access']);
        $this->assertIsString($response->data['jwt_refresh']);
        $this->assertSame(Http::OK, $response->code);
        $this->assertSame(Status::SUCCESS, $response->status);
        $this->assertSame('successfully authenticated user', $response->message);
        $this->assertObjectHasProperty('data', $response);
        $this->assertArrayNotHasKeyFromList($_POST, ['users_email', 'users_secret_code']);
    }

    /**
     * @throws ProcessException
     */
    #[Testing]
    public function auth2FAIsError(): void
    {
        $_POST['users_email'] = UsersFactory::USERS_EMAIL;

        $_POST['users_secret_code'] = fake()->numerify('######');

        $response = $this->loginController->auth2FA(
            new Users(),
            new Authenticator2FA(),
            new LoginModel(),
            (new LoginService())
                ->setJWT(new JWT())
                ->setRSA(new RSA())
                ->setLoginModel(new LoginModel())
                ->setAuthenticatorModel(new AuthenticatorModel())
                ->setAESService(
                    (new AESService())
                        ->setAES(new AES())
                )
                ->setJWTService(
                    (new JWTService())
                        ->setJWT(new JWT())
                        ->setRSA(new RSA())
                ),
            (new AuthenticatorService())
                ->setAuthenticatorModel(new AuthenticatorModel())
                ->setAuth2FA(new Auth2FA())
        );

        $this->assertIsObject($response);
        $this->assertInstanceOf(stdClass::class, $response);
        $this->assertObjectHasProperty('code', $response);
        $this->assertObjectHasProperty('status', $response);
        $this->assertObjectHasProperty('message', $response);
        $this->assertIsInt($response->code);
        $this->assertIsString($response->status);
        $this->assertIsString($response->message);
        $this->assertSame(Http::FORBIDDEN, $response->code);
        $this->assertSame(Status::ERROR, $response->status);
        $this->assertSame('2FA security is not active for this user', $response->message);
        $this->assertArrayNotHasKeyFromList($_POST, ['users_email', 'users_secret_code']);
    }

    /**
     * @throws AuthenticationException
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

        $_SERVER['HTTP_AUTHORIZATION'] = $this->getAuthorization([
            'session' => true,
            'idusers' => $encode['idusers'],
            'idroles' => $encode['idroles'],
        ]);

        $_POST['jwt_refresh'] = $jwtEncode['jwt_refresh'];

        $response = $this->loginController->refresh(
            new Authenticator2FA(),
            new Users(),
            (new LoginService())
                ->setJWT(new JWT())
                ->setRSA(new RSA())
                ->setLoginModel(new LoginModel())
                ->setAuthenticatorModel(new AuthenticatorModel())
                ->setAESService(
                    (new AESService())
                        ->setAES(new AES())
                )
                ->setJWTService(
                    (new JWTService())
                        ->setJWT(new JWT())
                        ->setRSA(new RSA())
                ),
            (new AESService())
                ->setAES(new AES()),
            (new JWTService())
                ->setJWT(new JWT())
                ->setRSA(new RSA())
        );

        $this->assertIsObject($response);
        $this->assertInstanceOf(stdClass::class, $response);
        $this->assertObjectHasProperty('code', $response);
        $this->assertObjectHasProperty('status', $response);
        $this->assertObjectHasProperty('message', $response);
        $this->assertObjectHasProperty('data', $response);
        $this->assertArrayHasKey('jwt_access', $response->data);
        $this->assertArrayHasKey('jwt_refresh', $response->data);
        $this->assertArrayHasKey('auth_2fa', $response->data);
        $this->assertIsInt($response->code);
        $this->assertIsString($response->status);
        $this->assertIsString($response->message);
        $this->assertIsArray($response->data);
        $this->assertIsString($response->data['jwt_access']);
        $this->assertIsString($response->data['jwt_refresh']);
        $this->assertIsBool($response->data['auth_2fa']);
        $this->assertSame(Http::OK, $response->code);
        $this->assertSame(Status::SUCCESS, $response->status);
        $this->assertSame('successfully authenticated user', $response->message);
        $this->assertHeaderNotHasKey('HTTP_AUTHORIZATION');
        $this->assertArrayNotHasKeyFromList($_POST, ['jwt_refresh']);
    }
}
