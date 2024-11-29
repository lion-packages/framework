<?php

declare(strict_types=1);

namespace Tests\App\Models\LionDatabase\MySQL;

use App\Enums\DocumentTypesEnum;
use App\Enums\RolesEnum;
use App\Models\LionDatabase\MySQL\ProfileModel;
use Database\Class\LionDatabase\MySQL\Users;
use Database\Migrations\LionDatabase\MySQL\Tables\DocumentTypes as DocumentTypesTable;
use Database\Migrations\LionDatabase\MySQL\Tables\Roles as RolesTable;
use Database\Migrations\LionDatabase\MySQL\Tables\Users as UsersTable;
use Database\Migrations\LionDatabase\MySQL\Views\ReadUsersById;
use Database\Seed\LionDatabase\MySQL\DocumentTypesSeed;
use Database\Seed\LionDatabase\MySQL\RolesSeed;
use Database\Seed\LionDatabase\MySQL\UsersSeed;
use Lion\Bundle\Test\Test;
use Lion\Request\Status;
use PHPUnit\Framework\Attributes\Test as Testing;
use stdClass;

class ProfileModelTest extends Test
{
    private ProfileModel $profileModel;
    private Users $users;

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

        $this->profileModel = new ProfileModel();

        $this->users = (new Users())
            ->setIdusers(1)
            ->setIdroles(RolesEnum::ADMINISTRATOR->value)
            ->setIddocumentTypes(DocumentTypesEnum::PASSPORT->value)
            ->setUsersCitizenIdentification(fake()->numerify('##########'))
            ->setUsersName(fake()->name())
            ->setUsersLastName(fake()->lastName());
    }

    #[Testing]
    public function readProfileDB(): void
    {
        /** @var stdClass $response */
        $response = $this->profileModel->readProfileDB($this->users);

        $this->assertIsObject($response);
        $this->assertInstanceOf(stdClass::class, $response);
        $this->assertObjectHasProperty('idusers', $response);
        $this->assertObjectHasProperty('idroles', $response);
        $this->assertObjectHasProperty('iddocument_types', $response);
        $this->assertObjectHasProperty('users_citizen_identification', $response);
        $this->assertObjectHasProperty('users_name', $response);
        $this->assertObjectHasProperty('users_last_name', $response);
        $this->assertObjectHasProperty('users_nickname', $response);
        $this->assertObjectHasProperty('users_email', $response);
        $this->assertIsInt($response->idusers);
        $this->assertIsInt($response->idroles);
        $this->assertIsInt($response->iddocument_types);
        $this->assertIsString($response->users_citizen_identification);
        $this->assertIsString($response->users_name);
        $this->assertIsString($response->users_last_name);
        $this->assertIsString($response->users_nickname);
        $this->assertIsString($response->users_email);
    }

    #[Testing]
    public function updateProfileDB(): void
    {
        $response = $this->profileModel->updateProfileDB($this->users);

        $this->assertIsObject($response);
        $this->assertInstanceOf(stdClass::class, $response);
        $this->assertObjectHasProperty('status', $response);
        $this->assertIsString($response->status);
        $this->assertSame(Status::SUCCESS, $response->status);

        /** @var stdClass $response */
        $response = $this->profileModel->readProfileDB(
            $this->users
                ->setIdusers(2)
        );

        $this->assertIsObject($response);
        $this->assertInstanceOf(stdClass::class, $response);
        $this->assertObjectHasProperty('idusers', $response);
        $this->assertObjectHasProperty('idroles', $response);
        $this->assertObjectHasProperty('iddocument_types', $response);
        $this->assertObjectHasProperty('users_citizen_identification', $response);
        $this->assertObjectHasProperty('users_name', $response);
        $this->assertObjectHasProperty('users_last_name', $response);
        $this->assertObjectHasProperty('users_nickname', $response);
        $this->assertObjectHasProperty('users_email', $response);
        $this->assertIsInt($response->idusers);
        $this->assertIsInt($response->idroles);
        $this->assertIsInt($response->iddocument_types);
        $this->assertIsString($response->users_citizen_identification);
        $this->assertIsString($response->users_name);
        $this->assertIsString($response->users_last_name);
        $this->assertIsString($response->users_nickname);
        $this->assertIsString($response->users_email);
        $this->assertSame(RolesEnum::MANAGER->value, $response->idroles);
    }
}
