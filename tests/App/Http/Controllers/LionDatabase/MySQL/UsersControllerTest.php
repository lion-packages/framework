<?php

declare(strict_types=1);

namespace Tests\App\Http\Controllers\LionDatabase\MySQL;

use App\Enums\DocumentTypesEnum;
use App\Enums\RolesEnum;
use App\Exceptions\ProcessException;
use App\Http\Controllers\LionDatabase\MySQL\UsersController;
use App\Models\LionDatabase\MySQL\UsersModel;
use Database\Class\LionDatabase\MySQL\Users;
use Database\Factory\LionDatabase\MySQL\UsersFactory;
use Exception;
use Lion\Database\Drivers\Schema\MySQL as Schema;
use Lion\Request\Http;
use Lion\Request\Status;
use Lion\Security\Validation;
use PHPUnit\Framework\Attributes\Test as Testing;
use Tests\Providers\AuthJwtProviderTrait;
use Tests\Providers\SetUpMigrationsAndQueuesProviderTrait;
use Tests\Test;

class UsersControllerTest extends Test
{
    use AuthJwtProviderTrait;
    use SetUpMigrationsAndQueuesProviderTrait;

    private UsersController $usersController;

    protected function setUp(): void
    {
        $this->runMigrations();

        $this->usersController = new UsersController();
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

        $response = $this->usersController->createUsers(new Users(), new UsersModel(), new Validation());

        $this->assertIsSuccess($response);
        $this->assertSame(Http::OK, $response->code);
        $this->assertSame(Status::SUCCESS, $response->status);
        $this->assertSame('registered user successfully', $response->message);
    }

    #[Testing]
    public function createUsersIsError(): void
    {
        $this
            ->exception(ProcessException::class)
            ->exceptionCode(Http::INTERNAL_SERVER_ERROR)
            ->exceptionStatus(Status::ERROR)
            ->exceptionMessage('an error occurred while registering the user')
            ->expectLionException(function (): void {
                $_POST['idroles'] = RolesEnum::ADMINISTRATOR->value;

                $_POST['iddocument_types'] = DocumentTypesEnum::PASSPORT->value;

                $_POST['users_citizen_identification'] = fake()->numerify('##########');

                $_POST['users_name'] = fake()->name();

                $_POST['users_last_name'] = fake()->lastName();

                $_POST['users_nickname'] = fake()->userName();

                $_POST['users_email'] = null;

                $_POST['users_password'] = (new Validation())->sha256(UsersFactory::USERS_PASSWORD);

                $this->usersController->createUsers(new Users(), new UsersModel(), new Validation());
            });
    }

    public function testReadUsers(): void
    {
        $response = $this->usersController->readUsers(new UsersModel());

        $this->assertIsArray($response);
        $this->assertCount(self::AVAILABLE_USERS, $response);
    }

    public function testReadUsersWithoutData(): void
    {
        Schema::truncateTable('users')->execute();

        $response = $this->usersController->readUsers(new UsersModel());

        $this->assertIsSuccess($response);
        $this->assertSame(Http::OK, $response->code);
        $this->assertSame(Status::SUCCESS, $response->status);
        $this->assertSame('no data available', $response->message);
    }

    public function testReadUsersById(): void
    {
        $response = $this->usersController->readUsers(new UsersModel());

        $this->assertIsArray($response);
        $this->assertCount(self::AVAILABLE_USERS, $response);

        $user = reset($response);

        $this->assertIsObject($user);
        $this->assertObjectHasProperty('idusers', $user);

        $response = $this->usersController->readUsersById(new Users(), new UsersModel(), (string) $user->idusers);

        $this->assertIsObject($response);
        $this->assertObjectHasProperty('idusers', $response);
        $this->assertSame($user->idusers, $response->idusers);
    }

    public function testReadUsersByIdWithoutData(): void
    {
        Schema::truncateTable('users')->execute();

        $response = $this->usersController->readUsersById(new Users(), new UsersModel(), "1");

        $this->assertIsSuccess($response);
        $this->assertSame(Http::OK, $response->code);
        $this->assertSame(Status::SUCCESS, $response->status);
        $this->assertSame('no data available', $response->message);
    }

    public function testUpdateUsers(): void
    {
        $response = $this->usersController->readUsers(new UsersModel());

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

        $response = $this->usersController->updateUsers(new Users(), new UsersModel(), (string) $user->idusers);

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

        $this->usersController->updateUsers(
            new Users(),
            new UsersModel(),
            fake()->numerify('###############')
        );
    }

    public function testDeleteUsers(): void
    {
        $response = $this->usersController->readUsers(new UsersModel());

        $this->assertIsArray($response);
        $this->assertCount(self::AVAILABLE_USERS, $response);

        $user = reset($response);

        $this->assertIsObject($user);
        $this->assertObjectHasProperty('idusers', $user);

        $response = $this->usersController->deleteUsers(new Users(), new UsersModel(), (string) $user->idusers);

        $this->assertIsSuccess($response);
        $this->assertSame(Http::OK, $response->code);
        $this->assertSame(Status::SUCCESS, $response->status);
        $this->assertSame('the registered user has been successfully deleted', $response->message);

        $response = $this->usersController->readUsers(new UsersModel());

        $this->assertIsArray($response);
        $this->assertCount(self::REMAINING_USERS, $response);
    }

    public function testDeleteUsersIsError(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('an error occurred while deleting the user');
        $this->expectExceptionCode(Http::INTERNAL_SERVER_ERROR);

        $this->usersController->deleteUsers(new Users(), new UsersModel(), fake()->numerify('###############'));
    }
}
