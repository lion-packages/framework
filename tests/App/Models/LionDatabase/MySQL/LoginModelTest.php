<?php

declare(strict_types=1);

namespace Tests\App\Models\LionDatabase\MySQL;

use App\Models\LionDatabase\MySQL\LoginModel;
use Database\Class\LionDatabase\MySQL\Users;
use Database\Factory\LionDatabase\MySQL\UsersFactory;
use Database\Migrations\LionDatabase\MySQL\Tables\DocumentTypes as DocumentTypesTable;
use Database\Migrations\LionDatabase\MySQL\Tables\Roles as RolesTable;
use Database\Migrations\LionDatabase\MySQL\Tables\Users as UsersTable;
use Database\Migrations\LionDatabase\MySQL\Views\ReadUsersById;
use Database\Seed\LionDatabase\MySQL\DocumentTypesSeed;
use Database\Seed\LionDatabase\MySQL\RolesSeed;
use Database\Seed\LionDatabase\MySQL\UsersSeed;
use Lion\Bundle\Test\Test;
use PHPUnit\Framework\Attributes\Test as Testing;
use stdClass;

class LoginModelTest extends Test
{
    private const string USERS_EMAIL_ERR = 'sleon@dev.com';

    private LoginModel $loginModel;

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

        $this->loginModel = new LoginModel();
    }

    #[Testing]
    public function authDB(): void
    {
        $response = $this->loginModel->authDB(
            (new Users())
                ->setUsersEmail(UsersFactory::USERS_EMAIL)
        );

        $this->assertIsObject($response);
        $this->assertInstanceOf(stdClass::class, $response);
        $this->assertObjectHasProperty('count', $response);
        $this->assertIsInt($response->count);
        $this->assertSame(1, $response->count);
    }

    #[Testing]
    public function authEmptyDB(): void
    {
        $response = $this->loginModel->authDB(
            (new Users())
                ->setUsersEmail(self::USERS_EMAIL_ERR)
        );

        $this->assertIsObject($response);
        $this->assertInstanceOf(stdClass::class, $response);
        $this->assertObjectHasProperty('count', $response);
        $this->assertIsInt($response->count);
        $this->assertSame(0, $response->count);
    }

    #[Testing]
    public function verifyAccountActivationDB(): void
    {
        /** @var stdClass $response */
        $response = $this->loginModel->verifyAccountActivationDB(
            (new Users())
                ->setUsersEmail(UsersFactory::USERS_EMAIL)
        );

        $this->assertIsObject($response);
        $this->assertInstanceOf(stdClass::class, $response);
        $this->assertObjectHasProperty('users_activation_code', $response);
        $this->assertNull($response->users_activation_code);
    }

    #[Testing]
    public function sessionDB(): void
    {
        $users = (new Users())
            ->setUsersEmail(UsersFactory::USERS_EMAIL);

        /** @var Users $response */
        $response = $this->loginModel->sessionDB($users);

        $this->assertIsObject($response);
        $this->assertInstanceOf(Users::class, $response);
        $this->assertObjectHasProperty('users_email', $response);
        $this->assertIsString($response->getUsersEmail());
        $this->assertSame($users->getUsersEmail(), $response->getUsersEmail());
    }
}
