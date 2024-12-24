<?php

declare(strict_types=1);

namespace Tests\App\Http\Controllers\LionDatabase\MySQL;

use App\Exceptions\AccountException;
use App\Exceptions\AuthenticationException;
use App\Http\Controllers\LionDatabase\MySQL\RegistrationController;
use App\Http\Services\AESService;
use App\Http\Services\LionDatabase\MySQL\AccountService;
use App\Http\Services\LionDatabase\MySQL\RegistrationService;
use App\Models\LionDatabase\MySQL\RegistrationModel;
use App\Models\LionDatabase\MySQL\UsersModel;
use Database\Class\LionDatabase\MySQL\Users;
use Database\Factory\LionDatabase\MySQL\UsersFactory;
use Database\Migrations\LionDatabase\MySQL\StoreProcedures\CreateUsers;
use Database\Migrations\LionDatabase\MySQL\StoreProcedures\UpdateActivationCode;
use Database\Migrations\LionDatabase\MySQL\Tables\DocumentTypes as DocumentTypesTable;
use Database\Migrations\LionDatabase\MySQL\Tables\Roles as RolesTable;
use Database\Migrations\LionDatabase\MySQL\Tables\Users as UsersTable;
use Database\Migrations\LionDatabase\MySQL\Views\ReadUsersById;
use Database\Seed\LionDatabase\MySQL\DocumentTypesSeed;
use Database\Seed\LionDatabase\MySQL\RolesSeed;
use Database\Seed\LionDatabase\MySQL\UsersSeed;
use Lion\Bundle\Helpers\Commands\Schedule\TaskQueue;
use Lion\Bundle\Helpers\Redis;
use Lion\Bundle\Test\Test;
use Lion\Database\Drivers\MySQL as DB;
use Lion\Database\Drivers\Schema\MySQL as Schema;
use Lion\Request\Http;
use Lion\Request\Status;
use Lion\Security\AES;
use Lion\Security\Validation;
use PHPUnit\Framework\Attributes\Test as Testing;
use stdClass;
use Tests\Providers\AuthJwtProviderTrait;

class RegistrationControllerTest extends Test
{
    use AuthJwtProviderTrait;

    private RegistrationController $registrationController;

    protected function setUp(): void
    {
        $this->executeMigrationsGroup([
            DocumentTypesTable::class,
            RolesTable::class,
            UsersTable::class,
            ReadUsersById::class,
            CreateUsers::class,
            UpdateActivationCode::class,
        ]);

        $this->executeSeedsGroup([
            DocumentTypesSeed::class,
            RolesSeed::class,
            UsersSeed::class,
        ]);

        $this->registrationController = new RegistrationController();
    }

    /**
     * @throws AccountException
     */
    #[Testing]
    public function register(): void
    {
        Schema::truncateTable('users')->execute();

        $encode = $this->AESEncode([
            'users_password' => UsersFactory::USERS_PASSWORD,
        ]);

        $_POST['users_email'] = UsersFactory::USERS_EMAIL;

        $_POST['users_password'] = $encode['users_password'];

        $response = $this->registrationController->register(
            new Users(),
            new UsersModel(),
            new RegistrationModel(),
            (new AccountService())
                ->setUsersModel(new UsersModel()),
            (new AESService())
                ->setAES(new AES()),
            new Validation(),
            (new TaskQueue())
                ->setRedis(new Redis())
        );

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

        $this->assertSame(
            'user successfully registered, check your mailbox to obtain the account activation code',
            $response->message
        );

        $this->assertHttpBodyNotHasKey('users_email');
        $this->assertHttpBodyNotHasKey('users_password');
    }

    /**
     * @throws AccountException
     * @throws AuthenticationException
     */
    #[Testing]
    public function verifyAccount(): void
    {
        Schema::truncateTable('users')->execute();

        $encode = $this->AESEncode([
            'users_password' => UsersFactory::USERS_PASSWORD,
        ]);

        $_POST['users_email'] = UsersFactory::USERS_EMAIL;

        $_POST['users_password'] = $encode['users_password'];

        $response = $this->registrationController->register(
            new Users(),
            new UsersModel(),
            new RegistrationModel(),
            (new AccountService())
                ->setUsersModel(new UsersModel()),
            (new AESService())
                ->setAES(new AES()),
            new Validation(),
            (new TaskQueue())
                ->setRedis(new Redis())
        );

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

        $this->assertSame(
            'user successfully registered, check your mailbox to obtain the account activation code',
            $response->message
        );

        /** @var stdClass $users_activation_code */
        $users_activation_code = DB::table('users')
            ->select('users_activation_code')
            ->where()->equalTo('users_email', UsersFactory::USERS_EMAIL)
            ->get();

        $_POST['users_activation_code'] = $users_activation_code->users_activation_code;

        $response = $this->registrationController->verifyAccount(
            new Users(),
            new RegistrationModel(),
            new RegistrationService(),
            (new AccountService())
                ->setUsersModel(new UsersModel()),
        );

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
        $this->assertSame('user account has been successfully verified', $response->message);
        $this->assertHttpBodyNotHasKey('users_email');
        $this->assertHttpBodyNotHasKey('users_password');
        $this->assertHttpBodyNotHasKey('users_activation_code');
    }
}
