<?php

declare(strict_types=1);

namespace Tests\Global\App\Http\Controllers\LionDatabase\MySQL;

use App\Http\Controllers\LionDatabase\MySQL\RegistrationController;
use Database\Factory\LionDatabase\MySQL\UsersFactory;
use Lion\Database\Drivers\MySQL as DB;
use Lion\Database\Drivers\Schema\MySQL as Schema;
use Lion\Dependency\Injection\Container;
use Lion\Request\Http;
use Lion\Request\Status;
use Lion\Security\Validation;
use Tests\Providers\SetUpMigrationsAndQueuesProviderTrait;
use Tests\Test;

class RegistrationControllerTest extends Test
{
    use SetUpMigrationsAndQueuesProviderTrait;

    const string USERS_EMAIL = 'root@dev.com';

    private RegistrationController $registrationController;
    private Container $container;

    protected function setUp(): void
    {
        $this->registrationController = new RegistrationController();

        $this->container = new Container();
    }

    protected function tearDown(): void
    {
        Schema::truncateTable('users')->execute();

        Schema::truncateTable('task_queue')->execute();
    }

    public function testRegister(): void
    {
        $_POST['users_email'] = self::USERS_EMAIL;

        $_POST['users_password'] = (new Validation())->sha256(UsersFactory::USERS_PASSWORD);

        $response = $this->container->injectDependenciesMethod($this->registrationController, 'register');

        $this->assertIsSuccess($response);
        $this->assertSame(Http::HTTP_OK, $response->code);
        $this->assertSame(Status::SUCCESS, $response->status);

        $this->assertSame(
            'user successfully registered, check your mailbox to obtain the account activation code',
            $response->message
        );

        $this->assertArrayNotHasKeyFromList($_POST, ['users_email', 'users_password']);
    }

    public function testVerifyAccount(): void
    {
        $_POST['users_email'] = self::USERS_EMAIL;

        $_POST['users_password'] = (new Validation())->sha256(UsersFactory::USERS_PASSWORD);

        $response = $this->container->injectDependenciesMethod($this->registrationController, 'register');

        $this->assertIsSuccess($response);
        $this->assertSame(Http::HTTP_OK, $response->code);
        $this->assertSame(Status::SUCCESS, $response->status);

        $this->assertSame(
            'user successfully registered, check your mailbox to obtain the account activation code',
            $response->message
        );

        $users_activation_code = DB::table('users')
            ->select('users_activation_code')
            ->where()->equalTo('users_email', self::USERS_EMAIL)
            ->get();

        $_POST['users_activation_code'] = $users_activation_code->users_activation_code;

        $response = $this->container->injectDependenciesMethod($this->registrationController, 'verifyAccount');

        $this->assertIsSuccess($response);
        $this->assertSame(Http::HTTP_OK, $response->code);
        $this->assertSame(Status::SUCCESS, $response->status);
        $this->assertSame('user account has been successfully verified', $response->message);
        $this->assertArrayNotHasKeyFromList($_POST, ['users_email', 'users_password', 'users_activation_code']);
    }
}
