<?php

declare(strict_types=1);

namespace Tests\App\Http\Services\LionDatabase\MySQL;

use App\Exceptions\AuthenticationException;
use App\Http\Services\LionDatabase\MySQL\RegistrationService;
use Database\Class\LionDatabase\MySQL\Users;
use Lion\Database\Drivers\Schema\MySQL as Schema;
use Lion\Exceptions\Exception;
use Lion\Request\Http;
use Lion\Request\Status;
use Lion\Test\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test as Testing;
use Tests\Providers\App\Http\Services\LionDatabase\MySQL\RegistrationServiceProviderTrait;
use Tests\Providers\SetUpMigrationsAndQueuesProviderTrait;

class RegistrationServiceTest extends Test
{
    use RegistrationServiceProviderTrait;
    use SetUpMigrationsAndQueuesProviderTrait;

    private RegistrationService $registrationService;

    protected function setUp(): void
    {
        $this->runMigrations();

        $this->registrationService = new RegistrationService();
    }

    protected function tearDown(): void
    {
        Schema::truncateTable('task_queue')->execute();
    }

    /**
     * @throws Exception
     */
    #[Testing]
    #[DataProvider('verifyAccountProvider')]
    public function verifyAccount(string $message, object $data, Users $users): void
    {
        $this
            ->exception(AuthenticationException::class)
            ->exceptionMessage($message)
            ->exceptionStatus(Status::SESSION_ERROR)
            ->exceptionCode(Http::FORBIDDEN)
            ->expectLionException(function () use ($data, $users): void {
                $this->registrationService->verifyAccount($users, $data);
            });
    }
}
