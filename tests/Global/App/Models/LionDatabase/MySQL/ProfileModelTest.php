<?php

declare(strict_types=1);

namespace Tests\Global\App\Models\LionDatabase\MySQL;

use App\Enums\DocumentTypesEnum;
use App\Enums\RolesEnum;
use App\Models\LionDatabase\MySQL\ProfileModel;
use Database\Class\LionDatabase\MySQL\Users;
use Lion\Database\Drivers\Schema\MySQL as Schema;
use Lion\Request\Status;
use Lion\Test\Test;
use Tests\Providers\SetUpMigrationsAndQueuesProviderTrait;

class ProfileModelTest extends Test
{
    use SetUpMigrationsAndQueuesProviderTrait;

    private ProfileModel $profileModel;
    private Users $users;

    protected function setUp(): void
    {
        $this->runMigrationsAndQueues();

        $this->profileModel = new ProfileModel();

        $this->users = (new Users())
            ->setIdusers(1)
            ->setIdroles(RolesEnum::ADMINISTRATOR->value)
            ->setIddocumentTypes(DocumentTypesEnum::PASSPORT->value)
            ->setUsersCitizenIdentification(fake()->numerify('##########'))
            ->setUsersName(fake()->name())
            ->setUsersLastName(fake()->lastName());
    }

    protected function tearDown(): void
    {
        Schema::truncateTable('users')->execute();
    }

    public function testReadProfileDB(): void
    {
        $response = $this->profileModel->readProfileDB($this->users);

        $this->assertIsObject($response);
        $this->assertObjectHasProperty('idusers', $response);
        $this->assertObjectHasProperty('idroles', $response);
        $this->assertObjectHasProperty('iddocument_types', $response);
        $this->assertObjectHasProperty('users_citizen_identification', $response);
        $this->assertObjectHasProperty('users_name', $response);
        $this->assertObjectHasProperty('users_last_name', $response);
        $this->assertObjectHasProperty('users_nickname', $response);
        $this->assertObjectHasProperty('users_email', $response);
    }

    public function testUpdateProfileDB(): void
    {
        $response = $this->profileModel->updateProfileDB($this->users);

        $this->assertIsObject($response);
        $this->assertObjectHasProperty('status', $response);
        $this->assertSame(Status::SUCCESS, $response->status);

        $response = $this->profileModel->readProfileDB(
            $this->users
                ->setIdusers(RolesEnum::MANAGER->value)
        );

        $this->assertIsObject($response);
        $this->assertObjectHasProperty('idroles', $response);
        $this->assertSame(RolesEnum::MANAGER->value, $response->idroles);
    }
}
