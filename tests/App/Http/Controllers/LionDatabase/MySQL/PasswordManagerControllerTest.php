<?php

declare(strict_types=1);

namespace Tests\App\Http\Controllers\LionDatabase\MySQL;

use App\Http\Controllers\LionDatabase\MySQL\PasswordManagerController;
use App\Models\LionDatabase\MySQL\UsersModel;
use Database\Class\LionDatabase\MySQL\Users;
use Database\Factory\LionDatabase\MySQL\UsersFactory;
use Lion\Database\Drivers\Schema\MySQL as Schema;
use Lion\Dependency\Injection\Container;
use Lion\Request\Http;
use Lion\Request\Status;
use Tests\Providers\AuthJwtProviderTrait;
use Tests\Providers\SetUpMigrationsAndQueuesProviderTrait;
use Tests\Test;

class PasswordManagerControllerTest extends Test
{
    use AuthJwtProviderTrait;
    use SetUpMigrationsAndQueuesProviderTrait;

    const string USERS_PASSWORD = 'lion-password';

    private PasswordManagerController $passwordManagerController;
    private Container $container;

    protected function setUp(): void
    {
        $this->runMigrationsAndQueues();

        $this->passwordManagerController = new PasswordManagerController();

        $this->container = new Container();
    }

    protected function tearDown(): void
    {
        Schema::truncateTable('users')->execute();

        Schema::truncateTable('task_queue')->execute();
    }

    public function testRecoveryPassword(): void
    {
        $_POST['users_email'] = UsersFactory::USERS_EMAIL;

        $response = $this->container->injectDependenciesMethod($this->passwordManagerController, 'recoveryPassword');

        $this->assertIsSuccess($response);
        $this->assertSame(Http::OK, $response->code);
        $this->assertSame(Status::SUCCESS, $response->status);

        $this->assertSame(
            'confirmation code sent, check your email inbox to see your verification code',
            $response->message
        );

        $this->assertArrayNotHasKeyFromList($_POST, ['users_email']);
    }

    public function testUpdateLostPassword(): void
    {
        $_POST['users_email'] = UsersFactory::USERS_EMAIL;

        $response = $this->container->injectDependenciesMethod($this->passwordManagerController, 'recoveryPassword');

        $this->assertIsSuccess($response);
        $this->assertSame(Http::OK, $response->code);
        $this->assertSame(Status::SUCCESS, $response->status);

        $this->assertSame(
            'confirmation code sent, check your email inbox to see your verification code',
            $response->message
        );

        $user = (new UsersModel())->readUsersByEmailDB(
            (new Users())
                ->setUsersEmail(UsersFactory::USERS_EMAIL)
        );

        $this->assertIsObject($user);
        $this->assertObjectHasProperty('users_recovery_code', $user);

        $encode = $this->AESEncode([
            'users_password_new' => self::USERS_PASSWORD,
            'users_password_confirm' => self::USERS_PASSWORD,
        ]);

        $_POST['users_password_new'] = $encode['users_password_new'];

        $_POST['users_password_confirm'] = $encode['users_password_confirm'];

        $_POST['users_recovery_code'] = $user->users_recovery_code;

        $response = $this->container->injectDependenciesMethod($this->passwordManagerController, 'updateLostPassword');

        $this->assertIsSuccess($response);
        $this->assertSame(Http::OK, $response->code);
        $this->assertSame(Status::SUCCESS, $response->status);

        $this->assertSame(
            'the recovery code is valid, your password has been updated successfully',
            $response->message
        );

        $this->assertArrayNotHasKeyFromList($_POST, [
            'users_email',
            'users_password_new',
            'users_password_confirm',
            'users_recovery_code',
        ]);
    }

    public function testUpdatePassword(): void
    {
        $users = (new UsersModel())->readUsersDB();

        $this->assertIsArray($users);

        $user = reset($users);

        $this->assertIsObject($user);
        $this->assertObjectHasProperty('idusers', $user);

        $encode = $this->AESEncode([
            'idusers' => (string) $user->idusers,
            'users_password' => UsersFactory::USERS_PASSWORD,
            'users_password_new' => self::USERS_PASSWORD,
            'users_password_confirm' => self::USERS_PASSWORD,
        ]);

        $_POST['users_password'] = $encode['users_password'];

        $_POST['users_password_new'] = $encode['users_password_new'];

        $_POST['users_password_confirm'] = $encode['users_password_confirm'];

        $_SERVER['HTTP_AUTHORIZATION'] = $this->getAuthorization([
            'idusers' => $encode['idusers'],
        ]);

        $response = $this->container->injectDependenciesMethod($this->passwordManagerController, 'updatePassword');

        $this->assertIsSuccess($response);
        $this->assertSame(Http::OK, $response->code);
        $this->assertSame(Status::SUCCESS, $response->status);
        $this->assertSame('password updated successfully', $response->message);
        $this->assertHeaderNotHasKey('HTTP_AUTHORIZATION');

        $this->assertArrayNotHasKeyFromList($_POST, [
            'users_password',
            'users_password_new',
            'users_password_confirm',
        ]);
    }
}
