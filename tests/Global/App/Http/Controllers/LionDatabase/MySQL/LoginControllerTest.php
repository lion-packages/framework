<?php

declare(strict_types=1);

namespace Tests\Global\App\Http\Controllers\LionDatabase\MySQL;

use App\Http\Controllers\LionDatabase\MySQL\LoginController;
use Lion\Database\Drivers\Schema\MySQL as Schema;
use Lion\Dependency\Injection\Container;
use Lion\Request\Http;
use Lion\Request\Status;
use Tests\Providers\SetUpMigrationsAndQueuesProviderTrait;
use Tests\Test;

class LoginControllerTest extends Test
{
    use SetUpMigrationsAndQueuesProviderTrait;

    const API_URL = 'http://127.0.0.1:8000/api/auth/login';
    const USERS_EMAIL = 'root@dev.com';
    const USERS_EMAIL_MANAGER = 'manager@dev.com';
    const USERS_PASSWORD = 'fc59487712bbe89b488847b77b5744fb6b815b8fc65ef2ab18149958edb61464';

    private LoginController $loginController;
    private Container $container;

    protected function setUp(): void
    {
        $this->runMigrationsAndQueues();

        $this->loginController = new LoginController();

        $this->container = new Container();
    }

    protected function tearDown(): void
    {
        Schema::truncateTable('users')->execute();
    }

    public function testAuth(): void
    {
        $_POST['users_email'] = self::USERS_EMAIL;

        $_POST['users_password'] = self::USERS_PASSWORD;

        $response = $this->container->injectDependenciesMethod($this->loginController, 'auth');

        $this->assertIsSuccess($response);
        $this->assertSame(Http::HTTP_OK, $response->code);
        $this->assertSame(Status::SUCCESS, $response->status);
        $this->assertSame('successfully authenticated user', $response->message);
        $this->assertObjectHasProperty('data', $response);
        $this->assertIsArray($response->data);
        $this->assertArrayHasKey('full_name', $response->data);
        $this->assertArrayHasKey('jwt', $response->data);
        $this->assertArrayNotHasKeyFromList($_POST, ['users_email', 'users_password']);
    }
}
