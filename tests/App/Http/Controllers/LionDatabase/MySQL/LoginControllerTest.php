<?php

declare(strict_types=1);

namespace Tests\App\Http\Controllers\LionDatabase\MySQL;

use App\Enums\RolesEnum;
use App\Exceptions\AuthenticationException;
use App\Http\Controllers\LionDatabase\MySQL\LoginController;
use App\Http\Services\AESService;
use App\Http\Services\JWTService;
use App\Http\Services\LionDatabase\MySQL\LoginService;
use App\Http\Services\LionDatabase\MySQL\PasswordManagerService;
use App\Models\LionDatabase\MySQL\LoginModel;
use Database\Class\LionDatabase\MySQL\Users;
use Database\Factory\LionDatabase\MySQL\UsersFactory;
use Lion\Database\Drivers\Schema\MySQL as Schema;
use Lion\Request\Http;
use Lion\Request\Status;
use Lion\Security\AES;
use Lion\Security\JWT;
use Lion\Security\RSA;
use Lion\Security\Validation;
use stdClass;
use Tests\Providers\AuthJwtProviderTrait;
use Tests\Providers\SetUpMigrationsAndQueuesProviderTrait;
use Tests\Test;

class LoginControllerTest extends Test
{
    use AuthJwtProviderTrait;
    use SetUpMigrationsAndQueuesProviderTrait;

    private LoginController $loginController;

    protected function setUp(): void
    {
        $this->runMigrationsAndQueues();

        $this->loginController = new LoginController();
    }

    protected function tearDown(): void
    {
        Schema::truncateTable('users')->execute();
    }

    public function testAuth(): void
    {
        $encode = $this->AESEncode([
            'users_password' => UsersFactory::USERS_PASSWORD,
        ]);

        $_POST['users_email'] = UsersFactory::USERS_EMAIL;

        $_POST['users_password'] = $encode['users_password'];

        $response = $this->loginController->auth(
            new Users(),
            new LoginModel(),
            (new LoginService())
                ->setJWT(new JWT())
                ->setRSA(new RSA())
                ->setLoginModel(new LoginModel())
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

        $this->assertIsSuccess($response);
        $this->assertSame(Http::OK, $response->code);
        $this->assertSame(Status::SUCCESS, $response->status);
        $this->assertSame('successfully authenticated user', $response->message);
        $this->assertObjectHasProperty('data', $response);
        $this->assertIsArray($response->data);
        $this->assertArrayHasKey('full_name', $response->data);
        $this->assertArrayHasKey('jwt_access', $response->data);
        $this->assertArrayHasKey('jwt_refresh', $response->data);
        $this->assertArrayNotHasKeyFromList($_POST, ['users_email', 'users_password']);
    }

    /**
     * @throws AuthenticationException
     */
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

        $_SERVER['HTTP_AUTHORIZATION'] = $this->getAuthorization([
            'session' => true,
            'idusers' => $encode['idusers'],
            'idroles' => $encode['idroles'],
        ]);

        $_POST['jwt_refresh'] = $jwtEncode['jwt_refresh'];

        $response = $this->loginController->refresh(
            (new LoginService())
                ->setJWT(new JWT())
                ->setRSA(new RSA())
                ->setLoginModel(new LoginModel())
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
        $this->assertSame(Http::OK, $response->code);
        $this->assertSame(Status::SUCCESS, $response->status);
        $this->assertSame('successfully authenticated user', $response->message);
        $this->assertIsArray($response->data);
        $this->assertArrayHasKey('jwt_access', $response->data);
        $this->assertArrayHasKey('jwt_refresh', $response->data);
        $this->assertIsString($response->data['jwt_access']);
        $this->assertIsString($response->data['jwt_refresh']);
        $this->assertHeaderNotHasKey('HTTP_AUTHORIZATION');
        $this->assertArrayNotHasKeyFromList($_POST, ['jwt_refresh']);
    }
}
