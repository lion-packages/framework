<?php

declare(strict_types=1);

namespace Tests\App\Http\Services\LionDatabase\MySQL;

use App\Exceptions\AuthenticationException;
use App\Http\Services\LionDatabase\MySQL\RegistrationService;
use Database\Class\LionDatabase\MySQL\Users;
use Lion\Database\Drivers\Schema\MySQL as Schema;
use Lion\Dependency\Injection\Container;
use Lion\Request\Request;
use Lion\Test\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Providers\App\Http\Services\LionDatabase\MySQL\RegistrationServiceProviderTrait;
use Tests\Providers\SetUpMigrationsAndQueuesProviderTrait;

class RegistrationServiceTest extends Test
{
    use RegistrationServiceProviderTrait;
    use SetUpMigrationsAndQueuesProviderTrait;

    private RegistrationService $registrationService;

    protected function setUp(): void
    {
        $this->runMigrationsAndQueues();

        $this->registrationService = (new Container())->injectDependencies(new RegistrationService());
    }

    protected function tearDown(): void
    {
        Schema::truncateTable('task_queue')->execute();
    }

    #[DataProvider('verifyAccountProvider')]
    public function testVerifyAccount(string $message, object $data, Users $users): void
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionCode(Request::HTTP_FORBIDDEN);
        $this->expectExceptionMessage($message);

        $this->registrationService->verifyAccount($users, $data);
    }
}
