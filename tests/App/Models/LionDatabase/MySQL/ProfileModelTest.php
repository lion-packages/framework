<?php

declare(strict_types=1);

namespace Tests\App\Models\LionDatabase\MySQL;

use App\Models\LionDatabase\MySQL\ProfileModel;
use Database\Class\LionDatabase\MySQL\Users;
use Lion\Test\Test;
use Tests\Providers\SetUpMigrationsAndQueuesProviderTrait;

class ProfileModelTest extends Test
{
    use SetUpMigrationsAndQueuesProviderTrait;

    const IDUSERS = 1;

    private ProfileModel $profileModel;

    protected function setUp(): void
    {
        $this->runMigrationsAndQueues();

        $this->profileModel = new ProfileModel();
    }

    protected function tearDown(): void
    {
    }

    public function testReadProfileDB(): void
    {
        $response = $this->profileModel->readProfileDB(
            (new Users())
                ->setIdusers(self::IDUSERS)
        );

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
}
