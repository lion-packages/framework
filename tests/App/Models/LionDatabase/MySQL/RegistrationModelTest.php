<?php

declare(strict_types=1);

namespace Tests\App\Models\LionDatabase\MySQL;

use App\Models\LionDatabase\MySQL\RegistrationModel;
use App\Models\LionDatabase\MySQL\UsersModel;
use Database\Class\LionDatabase\MySQL\Users;
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

class RegistrationModelTest extends Test
{
    private RegistrationModel $registrationModel;
    private UsersModel $usersModel;

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

        $this->registrationModel = new RegistrationModel();

        $this->usersModel = new UsersModel();
    }

    #[Testing]
    public function verifyAccountDB(): void
    {
        $users = $this->usersModel->readUsersDB();

        $this->assertIsArray($users);

        $row = reset($users);

        $this->assertIsObject($row);
        $this->assertInstanceOf(stdClass::class, $row);
        $this->assertObjectHasProperty('idusers', $row);
        $this->assertIsInt($row->idusers);

        /** @var stdClass $user */
        $user = $this->usersModel->readUsersByIdDB(
            (new Users())
                ->setIdusers($row->idusers)
        );

        $this->assertIsObject($user);
        $this->assertInstanceOf(stdClass::class, $user);
        $this->assertObjectHasProperty('idusers', $user);
        $this->assertIsString($user->users_email);

        /** @var stdClass $data */
        $data = $this->registrationModel->verifyAccountDB(
            (new Users())
                ->setUsersEmail($user->users_email)
        );

        $this->assertIsObject($data);
        $this->assertInstanceOf(stdClass::class, $data);
        $this->assertObjectHasProperty('idusers', $data);
        $this->assertObjectHasProperty('users_activation_code', $data);
        $this->assertIsInt($data->idusers);
        $this->assertSame($user->idusers, $data->idusers);
        $this->assertSame($user->users_activation_code, $data->users_activation_code);
    }

    #[Testing]
    public function validateAccountExistsDB(): void
    {
        $users = $this->usersModel->readUsersDB();

        $this->assertIsArray($users);

        $row = reset($users);

        $this->assertIsObject($row);
        $this->assertInstanceOf(stdClass::class, $row);
        $this->assertObjectHasProperty('idusers', $row);
        $this->assertIsInt($row->idusers);

        /** @var stdClass $user */
        $user = $this->usersModel->readUsersByIdDB(
            (new Users())
                ->setIdusers($row->idusers)
        );

        $this->assertIsObject($user);
        $this->assertInstanceOf(stdClass::class, $user);
        $this->assertObjectHasProperty('idusers', $user);
        $this->assertIsString($user->users_email);

        $cont = $this->registrationModel->validateAccountExistsDB(
            (new Users())
                ->setUsersEmail($user->users_email)
        );

        $this->assertIsObject($cont);
        $this->assertInstanceOf(stdClass::class, $cont);
        $this->assertObjectHasProperty('cont', $cont);
        $this->assertIsInt($cont->cont);
        $this->assertSame(1, $cont->cont);
    }
}
