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
use Database\Migrations\LionDatabase\MySQL\StoreProcedures\CreateUsers;
use Database\Migrations\LionDatabase\MySQL\StoreProcedures\DeleteUsers;
use Database\Migrations\LionDatabase\MySQL\StoreProcedures\UpdateUsers;
use Database\Migrations\LionDatabase\MySQL\Tables\DocumentTypes as DocumentTypesTable;
use Database\Migrations\LionDatabase\MySQL\Tables\Roles as RolesTable;
use Database\Migrations\LionDatabase\MySQL\Tables\Users as UsersTable;
use Database\Migrations\LionDatabase\MySQL\Views\ReadUsers;
use Database\Migrations\LionDatabase\MySQL\Views\ReadUsersById;
use Database\Seed\LionDatabase\MySQL\DocumentTypesSeed;
use Database\Seed\LionDatabase\MySQL\RolesSeed;
use Database\Seed\LionDatabase\MySQL\UsersSeed;
use Lion\Bundle\Test\Test;
use Lion\Database\Drivers\Schema\MySQL as Schema;
use Lion\Exceptions\Exception;
use Lion\Request\Http;
use Lion\Request\Status;
use Lion\Security\Validation;
use PHPUnit\Framework\Attributes\Test as Testing;
use stdClass;
use Tests\Providers\AuthJwtProviderTrait;

class UsersControllerTest extends Test
{
    use AuthJwtProviderTrait;

    private UsersController $usersController;

    protected function setUp(): void
    {
        $this->executeMigrationsGroup([
            DocumentTypesTable::class,
            RolesTable::class,
            UsersTable::class,
            ReadUsers::class,
            ReadUsersById::class,
            CreateUsers::class,
            DeleteUsers::class,
            UpdateUsers::class,
        ]);

        $this->executeSeedsGroup([
            DocumentTypesSeed::class,
            RolesSeed::class,
            UsersSeed::class,
        ]);

        $this->usersController = new UsersController();
    }

    protected function tearDown(): void
    {
        $this->assertHttpBodyNotHasKey('idroles');
        $this->assertHttpBodyNotHasKey('iddocument_types');
        $this->assertHttpBodyNotHasKey('users_citizen_identification');
        $this->assertHttpBodyNotHasKey('users_name');
        $this->assertHttpBodyNotHasKey('users_last_name');
        $this->assertHttpBodyNotHasKey('users_nickname');
        $this->assertHttpBodyNotHasKey('users_email');
        $this->assertHttpBodyNotHasKey('users_password');
    }

    /**
     * @throws ProcessException
     */
    #[Testing]
    public function createUsers(): void
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

        $this->assertIsObject($response);
        $this->assertInstanceOf(stdClass::class, $response);
        $this->assertObjectHasProperty('code', $response);
        $this->assertObjectHasProperty('status', $response);
        $this->assertObjectHasProperty('message', $response);
        $this->assertIsInt($response->code);
        $this->assertIsString($response->status);
        $this->assertIsString($response->message);
        $this->assertSame(Http::OK, $response->code);
        $this->assertSame(Status::SUCCESS, $response->status);
        $this->assertSame('registered user successfully', $response->message);
    }

    /**
     * @throws Exception
     * @throws ProcessException
     */
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

    #[Testing]
    public function readUsers(): void
    {
        $users = $this->usersController->readUsers(new UsersModel());

        $this->assertIsArray($users);

        $user = reset($users);

        $this->assertIsObject($user);
        $this->assertInstanceOf(stdClass::class, $user);
        $this->assertObjectHasProperty('idusers', $user);
        $this->assertObjectHasProperty('users_citizen_identification', $user);
        $this->assertObjectHasProperty('users_name', $user);
        $this->assertObjectHasProperty('users_last_name', $user);
        $this->assertObjectHasProperty('users_nickname', $user);
    }

    #[Testing]
    public function readUsersWithoutData(): void
    {
        Schema::truncateTable('users')->execute();

        $response = $this->usersController->readUsers(new UsersModel());

        $this->assertIsObject($response);
        $this->assertInstanceOf(stdClass::class, $response);
        $this->assertObjectHasProperty('code', $response);
        $this->assertObjectHasProperty('status', $response);
        $this->assertObjectHasProperty('message', $response);
        $this->assertIsInt($response->code);
        $this->assertIsString($response->status);
        $this->assertIsString($response->message);
        $this->assertSame(Http::OK, $response->code);
        $this->assertSame(Status::SUCCESS, $response->status);
        $this->assertSame('no data available', $response->message);
    }

    #[Testing]
    public function readUsersById(): void
    {
        $users = $this->usersController->readUsers(new UsersModel());

        $this->assertIsArray($users);

        $user = reset($users);

        $this->assertIsObject($user);
        $this->assertInstanceOf(stdClass::class, $user);
        $this->assertObjectHasProperty('idusers', $user);
        $this->assertObjectHasProperty('users_citizen_identification', $user);
        $this->assertObjectHasProperty('users_name', $user);
        $this->assertObjectHasProperty('users_last_name', $user);
        $this->assertObjectHasProperty('users_nickname', $user);

        /** @var stdClass $response */
        $response = $this->usersController->readUsersById(new Users(), new UsersModel(), (string) $user->idusers);

        $this->assertIsObject($response);
        $this->assertObjectHasProperty('idusers', $response);
        $this->assertSame($user->idusers, $response->idusers);
    }

    #[Testing]
    public function readUsersByIdWithoutData(): void
    {
        Schema::truncateTable('users')->execute();

        $response = $this->usersController->readUsersById(new Users(), new UsersModel(), "1");

        $this->assertIsObject($response);
        $this->assertInstanceOf(stdClass::class, $response);
        $this->assertObjectHasProperty('code', $response);
        $this->assertObjectHasProperty('status', $response);
        $this->assertObjectHasProperty('message', $response);
        $this->assertIsInt($response->code);
        $this->assertIsString($response->status);
        $this->assertIsString($response->message);
        $this->assertSame(Http::OK, $response->code);
        $this->assertSame(Status::SUCCESS, $response->status);
        $this->assertSame('no data available', $response->message);
    }

    /**
     * @throws ProcessException
     */
    #[Testing]
    public function updateUsers(): void
    {
        $users = $this->usersController->readUsers(new UsersModel());

        $this->assertIsArray($users);

        $user = reset($users);

        $this->assertIsObject($user);
        $this->assertInstanceOf(stdClass::class, $user);
        $this->assertObjectHasProperty('idusers', $user);
        $this->assertObjectHasProperty('users_citizen_identification', $user);
        $this->assertObjectHasProperty('users_name', $user);
        $this->assertObjectHasProperty('users_last_name', $user);
        $this->assertObjectHasProperty('users_nickname', $user);

        $_POST['idroles'] = 1;

        $_POST['iddocument_types'] = 1;

        $_POST['users_citizen_identification'] = '##########';

        $_POST['users_name'] = 'Sergio';

        $_POST['users_last_name'] = 'Leon';

        $_POST['users_nickname'] = 'Sleon';

        $_POST['users_email'] = 'sleon@dev.com';

        $response = $this->usersController->updateUsers(new Users(), new UsersModel(), (string) $user->idusers);

        $this->assertIsObject($response);
        $this->assertInstanceOf(stdClass::class, $response);
        $this->assertObjectHasProperty('code', $response);
        $this->assertObjectHasProperty('status', $response);
        $this->assertObjectHasProperty('message', $response);
        $this->assertIsInt($response->code);
        $this->assertIsString($response->status);
        $this->assertIsString($response->message);
        $this->assertSame(Http::OK, $response->code);
        $this->assertSame(Status::SUCCESS, $response->status);
        $this->assertSame('the registered user has been successfully updated', $response->message);
    }

    /**
     * @throws Exception
     * @throws ProcessException
     */
    #[Testing]
    public function updateUsersIsError(): void
    {
        $this
            ->exception(ProcessException::class)
            ->exceptionCode(Http::INTERNAL_SERVER_ERROR)
            ->exceptionStatus(Status::ERROR)
            ->exceptionMessage('an error occurred while updating the user')
            ->expectLionException(function (): void {
                $_POST['idroles'] = 1;

                $_POST['iddocument_types'] = 1;

                $_POST['users_citizen_identification'] = '##########';

                $_POST['users_name'] = 'Sergio';

                $_POST['users_last_name'] = 'Leon';

                $_POST['users_nickname'] = 'Sleon';

                $_POST['users_email'] = 'sleon@dev.com';

                $this->usersController->updateUsers(new Users(), new UsersModel(), fake()->numerify('###############'));
            });
    }

    /**
     * @throws ProcessException
     */
    #[Testing]
    public function deleteUsers(): void
    {
        $users = $this->usersController->readUsers(new UsersModel());

        $this->assertIsArray($users);

        $user = reset($users);

        $this->assertIsObject($user);
        $this->assertInstanceOf(stdClass::class, $user);
        $this->assertObjectHasProperty('idusers', $user);
        $this->assertObjectHasProperty('users_citizen_identification', $user);
        $this->assertObjectHasProperty('users_name', $user);
        $this->assertObjectHasProperty('users_last_name', $user);
        $this->assertObjectHasProperty('users_nickname', $user);

        $response = $this->usersController->deleteUsers(new Users(), new UsersModel(), (string) $user->idusers);

        $this->assertIsObject($response);
        $this->assertInstanceOf(stdClass::class, $response);
        $this->assertObjectHasProperty('code', $response);
        $this->assertObjectHasProperty('status', $response);
        $this->assertObjectHasProperty('message', $response);
        $this->assertIsInt($response->code);
        $this->assertIsString($response->status);
        $this->assertIsString($response->message);
        $this->assertSame(Http::OK, $response->code);
        $this->assertSame(Status::SUCCESS, $response->status);
        $this->assertSame('the registered user has been successfully deleted', $response->message);

        $response = $this->usersController->readUsers(new UsersModel());

        $this->assertIsArray($response);
        $this->assertCount(self::REMAINING_USERS, $response);
    }

    /**
     * @throws Exception
     * @throws ProcessException
     */
    #[Testing]
    public function deleteUsersIsError(): void
    {
        $this
            ->exception(ProcessException::class)
            ->exceptionCode(Http::INTERNAL_SERVER_ERROR)
            ->exceptionStatus(Status::ERROR)
            ->exceptionMessage('an error occurred while deleting the user')
            ->expectLionException(function (): void {
                $this->usersController->deleteUsers(new Users(), new UsersModel(), fake()->numerify('###############'));
            });
    }
}
