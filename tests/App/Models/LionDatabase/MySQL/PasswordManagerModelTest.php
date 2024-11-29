<?php

declare(strict_types=1);

namespace Tests\App\Models\LionDatabase\MySQL;

use App\Models\LionDatabase\MySQL\PasswordManagerModel;
use App\Models\LionDatabase\MySQL\UsersModel;
use Database\Class\PasswordManager;
use Database\Factory\LionDatabase\MySQL\UsersFactory;
use Database\Migrations\LionDatabase\MySQL\Tables\DocumentTypes as DocumentTypesTable;
use Database\Migrations\LionDatabase\MySQL\Tables\Roles as RolesTable;
use Database\Migrations\LionDatabase\MySQL\Tables\Users as UsersTable;
use Database\Migrations\LionDatabase\MySQL\Views\ReadUsersById;
use Database\Seed\LionDatabase\MySQL\DocumentTypesSeed;
use Database\Seed\LionDatabase\MySQL\RolesSeed;
use Database\Seed\LionDatabase\MySQL\UsersSeed;
use Lion\Bundle\Test\Test;
use Lion\Request\Status;
use Lion\Security\Validation;
use PHPUnit\Framework\Attributes\Test as Testing;
use stdClass;
use Tests\Providers\AuthJwtProviderTrait;

class PasswordManagerModelTest extends Test
{
    use AuthJwtProviderTrait;

    const string USERS_PASSWORD = 'lion-password';

    private PasswordManagerModel $passwordManagerModel;
    private Validation $validation;

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

        $this->passwordManagerModel = new PasswordManagerModel();

        $this->validation = new Validation();
    }

    #[Testing]
    public function getPasswordDB(): void
    {
        $users = (new UsersModel())->readUsersDB();

        $this->assertIsArray($users);

        $user = reset($users);

        $this->assertIsObject($user);
        $this->assertInstanceOf(stdClass::class, $user);
        $this->assertObjectHasProperty('idusers', $user);
        $this->assertIsInt($user->idusers);

        /** @var stdClass $hash */
        $hash = $this->passwordManagerModel->getPasswordDB(
            (new PasswordManager())
                ->setIdusers($user->idusers)
        );

        $this->assertIsObject($hash);
        $this->assertInstanceOf(stdClass::class, $hash);
        $this->assertObjectHasProperty('users_password', $hash);
        $this->assertIsString($hash->users_password);
        $this->assertTrue(password_verify(UsersFactory::USERS_PASSWORD, $hash->users_password));
    }

    #[Testing]
    public function updatePasswordDB(): void
    {
        $users = (new UsersModel())->readUsersDB();

        $this->assertIsArray($users);

        $user = reset($users);

        $this->assertIsObject($user);
        $this->assertInstanceOf(stdClass::class, $user);
        $this->assertObjectHasProperty('idusers', $user);
        $this->assertIsInt($user->idusers);

        $encode = $this->AESEncode(['users_password_confirm' => self::USERS_PASSWORD]);

        $response = $this->passwordManagerModel->updatePasswordDB(
            (new PasswordManager())
                ->setIdusers($user->idusers)
                ->setUsersPasswordConfirm($encode['users_password_confirm'])
        );

        $this->assertIsObject($response);
        $this->assertInstanceOf(stdClass::class, $response);
        $this->assertObjectHasProperty('status', $response);
        $this->assertIsString($response->status);
        $this->assertSame(Status::SUCCESS, $response->status);
    }
}
