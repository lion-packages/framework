<?php

declare(strict_types=1);

namespace Tests\App\Http\Controllers\LionDatabase\MySQL;

use App\Enums\DocumentTypesEnum;
use App\Enums\RolesEnum;
use App\Http\Controllers\LionDatabase\MySQL\UsersController;
use Database\Factory\LionDatabase\MySQL\UsersFactory;
use Exception;
use Lion\Database\Drivers\Schema\MySQL as Schema;
use Lion\Dependency\Injection\Container;
use Lion\Request\Http;
use Lion\Request\Status;
use Lion\Security\Validation;
use Tests\Providers\AuthJwtProviderTrait;
use Tests\Providers\SetUpMigrationsAndQueuesProviderTrait;
use Tests\Test;

class UsersControllerTest extends Test
{
    use AuthJwtProviderTrait;
    use SetUpMigrationsAndQueuesProviderTrait;

    private UsersController $usersController;
    private Container $container;

    protected function setUp(): void
    {
        $this->runMigrationsAndQueues();

        $this->usersController = new UsersController();

        $this->container = new Container();
    }

    protected function tearDown(): void
    {
        Schema::truncateTable('users')->execute();

        $this->assertArrayNotHasKeyFromList($_POST, [
            'idroles',
            'iddocument_types',
            'users_citizen_identification',
            'users_name',
            'users_last_name',
            'users_nickname',
            'users_email',
            'users_password'
        ]);
    }

    public function testCreateUsers(): void
    {
        $_POST['idroles'] = RolesEnum::ADMINISTRATOR->value;

        $_POST['iddocument_types'] = DocumentTypesEnum::PASSPORT->value;

        $_POST['users_citizen_identification'] = fake()->numerify('##########');

        $_POST['users_name'] = fake()->name();

        $_POST['users_last_name'] = fake()->lastName();

        $_POST['users_nickname'] = fake()->userName();

        $_POST['users_email'] = fake()->email();

        $_POST['users_password'] = (new Validation())->sha256(UsersFactory::USERS_PASSWORD);

        $response = $this->container->injectDependenciesMethod($this->usersController, 'createUsers');

        $this->assertIsSuccess($response);
        $this->assertSame(Http::OK, $response->code);
        $this->assertSame(Status::SUCCESS, $response->status);
        $this->assertSame('registered user successfully', $response->message);
    }

    public function testCreateUsersIsError(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('an error occurred while registering the user');
        $this->expectExceptionCode(Http::INTERNAL_SERVER_ERROR);

        $_POST['idroles'] = RolesEnum::ADMINISTRATOR->value;

        $_POST['iddocument_types'] = DocumentTypesEnum::PASSPORT->value;

        $_POST['users_citizen_identification'] = fake()->numerify('##########');

        $_POST['users_name'] = fake()->name();

        $_POST['users_last_name'] = fake()->lastName();

        $_POST['users_nickname'] = fake()->userName();

        $_POST['users_email'] = null;

        $_POST['users_password'] = (new Validation())->sha256(UsersFactory::USERS_PASSWORD);

        $this->container->injectDependenciesMethod($this->usersController, 'createUsers');
    }

    public function testReadUsers(): void
    {
        $response = $this->container->injectDependenciesMethod($this->usersController, 'readUsers');

        $this->assertIsArray($response);
        $this->assertCount(self::AVAILABLE_USERS, $response);
    }

    public function testReadUsersWithoutData(): void
    {
        Schema::truncateTable('users')->execute();

        $response = $this->container->injectDependenciesMethod($this->usersController, 'readUsers');

        $this->assertIsSuccess($response);
        $this->assertSame(Http::OK, $response->code);
        $this->assertSame(Status::SUCCESS, $response->status);
        $this->assertSame('no data available', $response->message);
    }

    public function testReadUsersById(): void
    {
        $response = $this->container->injectDependenciesMethod($this->usersController, 'readUsers');

        $this->assertIsArray($response);
        $this->assertCount(self::AVAILABLE_USERS, $response);

        $user = reset($response);

        $this->assertIsObject($user);
        $this->assertObjectHasProperty('idusers', $user);

        $response = $this->container->injectDependenciesMethod($this->usersController, 'readUsersById', [
            'idusers' => $user->idusers
        ]);

        $this->assertIsObject($response);
        $this->assertObjectHasProperty('idusers', $response);
        $this->assertSame($user->idusers, $response->idusers);
    }

    public function testReadUsersByIdWithoutData(): void
    {
        Schema::truncateTable('users')->execute();

        $response = $this->container->injectDependenciesMethod($this->usersController, 'readUsersById', [
            'idusers' => 1
        ]);

        $this->assertIsSuccess($response);
        $this->assertSame(Http::OK, $response->code);
        $this->assertSame(Status::SUCCESS, $response->status);
        $this->assertSame('no data available', $response->message);
    }

    public function testUpdateUsers(): void
    {
        $response = $this->container->injectDependenciesMethod($this->usersController, 'readUsers');

        $this->assertIsArray($response);
        $this->assertCount(self::AVAILABLE_USERS, $response);

        $user = reset($response);

        $this->assertIsObject($user);
        $this->assertObjectHasProperty('idusers', $user);

        $_POST['idroles'] = 1;

        $_POST['iddocument_types'] = 1;

        $_POST['users_citizen_identification'] = '##########';

        $_POST['users_name'] = 'Sergio';

        $_POST['users_last_name'] = 'Leon';

        $_POST['users_nickname'] = 'Sleon';

        $_POST['users_email'] = 'sleon@dev.com';

        $response = $this->container->injectDependenciesMethod($this->usersController, 'updateUsers', [
            'idusers' => $user->idusers
        ]);

        $this->assertIsSuccess($response);
        $this->assertSame(Http::OK, $response->code);
        $this->assertSame(Status::SUCCESS, $response->status);
        $this->assertSame('the registered user has been successfully updated', $response->message);
    }

    public function testUpdateUsersIsError(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('an error occurred while updating the user');
        $this->expectExceptionCode(Http::INTERNAL_SERVER_ERROR);

        $_POST['idroles'] = 1;

        $_POST['iddocument_types'] = 1;

        $_POST['users_citizen_identification'] = '##########';

        $_POST['users_name'] = 'Sergio';

        $_POST['users_last_name'] = 'Leon';

        $_POST['users_nickname'] = 'Sleon';

        $_POST['users_email'] = 'sleon@dev.com';

        $this->container->injectDependenciesMethod($this->usersController, 'updateUsers', [
            'idusers' => fake()->numerify('###############')
        ]);
    }

    public function testDeleteUsers(): void
    {
        $response = $this->container->injectDependenciesMethod($this->usersController, 'readUsers');

        $this->assertIsArray($response);
        $this->assertCount(self::AVAILABLE_USERS, $response);

        $user = reset($response);

        $this->assertIsObject($user);
        $this->assertObjectHasProperty('idusers', $user);

        $response = $this->container->injectDependenciesMethod($this->usersController, 'deleteUsers', [
            'idusers' => $user->idusers
        ]);

        $this->assertIsSuccess($response);
        $this->assertSame(Http::OK, $response->code);
        $this->assertSame(Status::SUCCESS, $response->status);
        $this->assertSame('the registered user has been successfully deleted', $response->message);

        $response = $this->container->injectDependenciesMethod($this->usersController, 'readUsers');

        $this->assertIsArray($response);
        $this->assertCount(self::REMAINING_USERS, $response);
    }

    public function testDeleteUsersIsError(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('an error occurred while deleting the user');
        $this->expectExceptionCode(Http::INTERNAL_SERVER_ERROR);

        $this->container->injectDependenciesMethod($this->usersController, 'deleteUsers', [
            'idusers' => fake()->numerify('###############')
        ]);
    }
}
