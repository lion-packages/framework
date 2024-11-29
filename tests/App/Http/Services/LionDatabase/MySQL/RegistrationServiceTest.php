<?php

declare(strict_types=1);

namespace Tests\App\Http\Services\LionDatabase\MySQL;

use App\Exceptions\AuthenticationException;
use App\Http\Services\LionDatabase\MySQL\RegistrationService;
use Database\Class\LionDatabase\MySQL\Users;
use Database\Migrations\LionDatabase\MySQL\Tables\DocumentTypes as DocumentTypesTable;
use Database\Migrations\LionDatabase\MySQL\Tables\Roles as RolesTable;
use Database\Migrations\LionDatabase\MySQL\Tables\Users as UsersTable;
use Database\Migrations\LionDatabase\MySQL\Views\ReadUsersById;
use Database\Seed\LionDatabase\MySQL\DocumentTypesSeed;
use Database\Seed\LionDatabase\MySQL\RolesSeed;
use Database\Seed\LionDatabase\MySQL\UsersSeed;
use Lion\Bundle\Test\Test;
use Lion\Exceptions\Exception;
use Lion\Request\Http;
use Lion\Request\Status;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test as Testing;
use Tests\Providers\App\Http\Services\LionDatabase\MySQL\RegistrationServiceProviderTrait;

class RegistrationServiceTest extends Test
{
    use RegistrationServiceProviderTrait;

    private RegistrationService $registrationService;

    protected function setUp(): void
    {
        $this->executeMigrationsGroup([
            DocumentTypesTable::class,
            RolesTable::class,
            UsersTable::class,
            ReadUsersById::class,
        ]);

        $this->executeSeedsGroup([
            DocumentTypesSeed::class,
            RolesSeed::class,
            UsersSeed::class,
        ]);

        $this->registrationService = new RegistrationService();
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
