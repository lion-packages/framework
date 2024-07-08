<?php

declare(strict_types=1);

namespace Tests\App\Http\Controllers\LionDatabase\MySQL;

use App\Http\Controllers\LionDatabase\MySQL\AuthenticatorController;
use App\Http\Services\AESService;
use App\Http\Services\JWTService;
use App\Http\Services\LionDatabase\MySQL\AuthenticatorService;
use App\Models\LionDatabase\MySQL\AuthenticatorModel;
use App\Models\LionDatabase\MySQL\UsersModel;
use Database\Class\Authenticator2FA;
use Database\Class\LionDatabase\MySQL\Users;
use Database\Factory\LionDatabase\MySQL\UsersFactory;
use Lion\Authentication\Auth2FA;
use Lion\Database\Drivers\Schema\MySQL as Schema;
use Lion\Request\Http;
use Lion\Request\Status;
use Lion\Security\AES;
use Lion\Security\JWT;
use Lion\Security\RSA;
use PHPUnit\Framework\Attributes\Test as Testing;
use PragmaRX\Google2FAQRCode\Google2FA;
use Tests\Providers\AuthJwtProviderTrait;
use Tests\Providers\SetUpMigrationsAndQueuesProviderTrait;
use Tests\Test;

class AuthenticatorControllerTest extends Test
{
    use AuthJwtProviderTrait;
    use SetUpMigrationsAndQueuesProviderTrait;

    private AuthenticatorController $authenticatorController;

    protected function setUp(): void
    {
        $this->runMigrationsAndQueues();

        $this->authenticatorController = new AuthenticatorController();
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

        $_POST['users_password'] = $aesEncode['users_password'];

        $_SERVER['HTTP_AUTHORIZATION'] = $this->getAuthorization([
            'idusers' => $aesEncode['idusers'],
        ]);

        $response = $this->authenticatorController->passwordVerify(
            new Users(),
            (new AuthenticatorService())
                ->setAuthenticatorModel(new AuthenticatorModel()),
            (new JWTService())
                ->setRSA(new RSA())
                ->setJWT(new JWT()),
            (new AESService())
                ->setAES(new AES())
        );

        $this->assertIsObject($response);
        $this->assertObjectHasProperty('code', $response);
        $this->assertObjectHasProperty('status', $response);
        $this->assertObjectHasProperty('message', $response);
        $this->assertSame(Http::OK, $response->code);
        $this->assertSame(Status::SUCCESS, $response->status);
        $this->assertSame('the password is valid', $response->message);
        $this->assertHeaderNotHasKey('HTTP_AUTHORIZATION');
        $this->assertArrayNotHasKeyFromList($_POST, ['users_password']);
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

        $aesEncode = $this->AESEncode([
            'idusers' => (string) $user->idusers,
        ]);

        $_SERVER['HTTP_AUTHORIZATION'] = $this->getAuthorization([
            'idusers' => $aesEncode['idusers'],
        ]);

        $response = $this->authenticatorController->qr(
            new Users(),
            new Auth2FA(),
            new UsersModel(),
            (new AESService())
                ->setAES(new AES()),
            (new JWTService())
                ->setRSA(new RSA())
                ->setJWT(new JWT())
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
        $this->assertHeaderNotHasKey('HTTP_AUTHORIZATION');
    }

    #[Testing]
    public function enable2FA(): void
    {
        $users = (new UsersModel())->readUsersDB();

        $this->assertIsArray($users);
        $this->assertCount(2, $users);

        $user = reset($users);

        $this->assertIsObject($user);
        $this->assertObjectHasProperty('idusers', $user);

        $aesEncode = $this->AESEncode([
            'idusers' => (string) $user->idusers,
        ]);

        $_SERVER['HTTP_AUTHORIZATION'] = $this->getAuthorization([
            'idusers' => $aesEncode['idusers'],
        ]);

        $response = $this->authenticatorController->qr(
            new Users(),
            new Auth2FA(),
            new UsersModel(),
            (new AESService())
                ->setAES(new AES()),
            (new JWTService())
                ->setRSA(new RSA())
                ->setJWT(new JWT())
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
        $this->assertHeaderNotHasKey('HTTP_AUTHORIZATION');

        $aesDecode = $this->AESDecode([
            'secret' => $response->data->secret,
        ]);

        $_POST['users_2fa_secret'] = $response->data->secret;

        $_POST['users_secret_code'] = (new Google2FA())->getCurrentOtp($aesDecode['secret']);

        $_SERVER['HTTP_AUTHORIZATION'] = $this->getAuthorization([
            'idusers' => $aesEncode['idusers'],
        ]);

        $response = $this->authenticatorController->enable2FA(
            new Authenticator2FA(),
            (new AuthenticatorService())
                ->setAuthenticatorModel(new AuthenticatorModel())
                ->setAuth2FA(new Auth2FA()),
            (new AESService())
                ->setAES(new AES()),
            (new JWTService())
                ->setRSA(new RSA())
                ->setJWT(new JWT())
        );

        $this->assertIsObject($response);
        $this->assertObjectHasProperty('code', $response);
        $this->assertObjectHasProperty('status', $response);
        $this->assertObjectHasProperty('message', $response);
        $this->assertSame(Http::OK, $response->code);
        $this->assertSame(Status::SUCCESS, $response->status);
        $this->assertSame('2FA authentication has been enabled', $response->message);
        $this->assertHeaderNotHasKey('HTTP_AUTHORIZATION');
        $this->assertArrayNotHasKeyFromList($_POST, ['users_2fa_secret', 'users_secret_code']);

        $user = (new UsersModel())
            ->readUsersByIdDB(
                (new Users())
                    ->setIdusers($user->idusers)
            );

        $this->assertIsObject($user);
        $this->assertObjectHasProperty('users_2fa', $user);
        $this->assertObjectHasProperty('users_2fa_secret', $user);
        $this->assertSame(UsersFactory::ENABLED_2FA, $user->users_2fa);
        $this->assertSame($aesDecode['secret'], $user->users_2fa_secret);
    }
}
