<?php

declare(strict_types=1);

namespace Tests\App\Http\Controllers\LionDatabase\MySQL;

use App\Http\Controllers\LionDatabase\MySQL\ProfileController;
use App\Http\Services\AESService;
use App\Http\Services\JWTService;
use App\Models\LionDatabase\MySQL\ProfileModel;
use App\Models\LionDatabase\MySQL\UsersModel;
use Database\Class\LionDatabase\MySQL\Users;
use Exception;
use Lion\Database\Drivers\Schema\MySQL as Schema;
use Lion\Request\Http;
use Lion\Request\Status;
use Lion\Security\AES;
use Lion\Security\JWT;
use Lion\Security\RSA;
use Tests\Providers\AuthJwtProviderTrait;
use Tests\Providers\SetUpMigrationsAndQueuesProviderTrait;
use Tests\Test;

class ProfileControllerTest extends Test
{
    use AuthJwtProviderTrait;
    use SetUpMigrationsAndQueuesProviderTrait;

    const string USERS_EMAIL = 'root@dev.com';

    private ProfileController $profileController;

    protected function setUp(): void
    {
        $this->runMigrations();

        $this->profileController = new ProfileController();
    }

    protected function tearDown(): void
    {
        $this->assertHeaderNotHasKey('HTTP_AUTHORIZATION');

        Schema::truncateTable('users')->execute();
    }

    public function testReadProfile(): void
    {
        $users = (new UsersModel())->readUsersDB();

        $this->assertIsArray($users);

        $user = reset($users);

        $this->assertIsObject($user);
        $this->assertObjectHasProperty('idusers', $user);

        $encode = $this->AESEncode(['idusers' => (string) $user->idusers]);

        $_SERVER['HTTP_AUTHORIZATION'] = $this->getAuthorization([
            'idusers' => $encode['idusers']
        ]);

        $response = $this->profileController->readProfile(
            new Users(),
            new ProfileModel(),
            (new JWTService())
                ->setRSA(new RSA())
                ->setJWT(new JWT()),
            (new AESService())
                ->setAES(new AES())
        );

        $this->assertIsObject($response);
        $this->assertObjectHasProperty('idusers', $response);
        $this->assertObjectHasProperty('idroles', $response);
        $this->assertObjectHasProperty('iddocument_types', $response);
        $this->assertObjectHasProperty('users_citizen_identification', $response);
        $this->assertObjectHasProperty('users_name', $response);
        $this->assertObjectHasProperty('users_last_name', $response);
        $this->assertObjectHasProperty('users_nickname', $response);
        $this->assertObjectHasProperty('users_email', $response);
        $this->assertSame($user->idusers, $response->idusers);
    }

    public function testUpdateProfile(): void
    {
        $users = (new UsersModel())->readUsersDB();

        $this->assertIsArray($users);

        $user = reset($users);

        $this->assertIsObject($user);
        $this->assertObjectHasProperty('idusers', $user);

        $encode = $this->AESEncode(['idusers' => (string) $user->idusers]);

        $_SERVER['HTTP_AUTHORIZATION'] = $this->getAuthorization([
            'idusers' => $encode['idusers']
        ]);

        $users_name = fake()->name();

        $_POST['users_name'] = $users_name;

        $response = $this->profileController->updateProfile(
            new Users(),
            new ProfileModel(),
            (new JWTService())
                ->setRSA(new RSA())
                ->setJWT(new JWT()),
            (new AESService())
                ->setAES(new AES())
        );

        $this->assertIsSuccess($response);
        $this->assertSame(Http::OK, $response->code);
        $this->assertSame(Status::SUCCESS, $response->status);
        $this->assertSame('profile updated successfully', $response->message);
        $this->assertArrayNotHasKeyFromList($_POST, ['users_name']);

        $users = (new UsersModel())->readUsersDB();

        $this->assertIsArray($users);

        $user = reset($users);

        $this->assertIsObject($user);
        $this->assertObjectHasProperty('users_name', $user);
        $this->assertSame($users_name, $user->users_name);
    }

    public function testUpdateProfileIsError(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("an error occurred while updating the user's profile");
        $this->expectExceptionCode(Http::INTERNAL_SERVER_ERROR);

        $encode = $this->AESEncode(['idusers' => fake()->numerify('###############')]);

        $_SERVER['HTTP_AUTHORIZATION'] = $this->getAuthorization([
            'idusers' => $encode['idusers']
        ]);

        $this->profileController->updateProfile(
            new Users(),
            new ProfileModel(),
            (new JWTService())
                ->setRSA(new RSA())
                ->setJWT(new JWT()),
            (new AESService())
                ->setAES(new AES())
        );
    }
}
