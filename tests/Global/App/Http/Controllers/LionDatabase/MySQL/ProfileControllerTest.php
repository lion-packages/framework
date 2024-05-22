<?php

declare(strict_types=1);

namespace Tests\Global\App\Http\Controllers\LionDatabase\MySQL;

use App\Http\Controllers\LionDatabase\MySQL\ProfileController;
use App\Models\LionDatabase\MySQL\UsersModel;
use Exception;
use Lion\Database\Drivers\Schema\MySQL as Schema;
use Lion\Dependency\Injection\Container;
use Lion\Request\Http;
use Lion\Request\Status;
use Tests\Providers\AuthJwtProviderTrait;
use Tests\Providers\SetUpMigrationsAndQueuesProviderTrait;
use Tests\Test;

class ProfileControllerTest extends Test
{
    use AuthJwtProviderTrait;
    use SetUpMigrationsAndQueuesProviderTrait;

    const USERS_EMAIL = 'root@dev.com';

    private ProfileController $profileController;
    private Container $container;

    protected function setUp(): void
    {
        $this->runMigrationsAndQueues();

        $this->profileController = new ProfileController();

        $this->container = new Container();
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

        $_SERVER['HTTP_AUTHORIZATION'] = $this->getAuthorization([
            'idusers' => $user->idusers
        ]);

        $response = $this->container->injectDependenciesMethod($this->profileController, 'readProfile');

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

        $_SERVER['HTTP_AUTHORIZATION'] = $this->getAuthorization([
            'idusers' => $user->idusers
        ]);

        $users_name = fake()->name();

        $_POST['users_name'] = $users_name;

        $response = $this->container->injectDependenciesMethod($this->profileController, 'updateProfile');

        $this->assertIsSuccess($response);
        $this->assertSame(Http::HTTP_OK, $response->code);
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
        $this->expectExceptionCode(Http::HTTP_INTERNAL_SERVER_ERROR);

        $_SERVER['HTTP_AUTHORIZATION'] = $this->getAuthorization([
            'idusers' => (int) fake()->numerify('###############')
        ]);

        $this->container->injectDependenciesMethod($this->profileController, 'updateProfile');
    }
}