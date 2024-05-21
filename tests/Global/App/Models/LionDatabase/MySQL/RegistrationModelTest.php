<?php

declare(strict_types=1);

namespace Tests\Global\App\Models\LionDatabase\MySQL;

use App\Models\LionDatabase\MySQL\RegistrationModel;
use App\Models\LionDatabase\MySQL\UsersModel;
use Database\Class\LionDatabase\MySQL\Users;
use Lion\Test\Test;
use Tests\Providers\SetUpMigrationsAndQueuesProviderTrait;

class RegistrationModelTest extends Test
{
    use SetUpMigrationsAndQueuesProviderTrait;

    private RegistrationModel $registrationModel;
    private UsersModel $usersModel;

    protected function setUp(): void
    {
        $this->runMigrationsAndQueues();

        $this->registrationModel = new RegistrationModel();

        $this->usersModel = new UsersModel();
    }

    public function testVerifyAccountDB(): void
    {
        $users = $this->usersModel->readUsersDB();

        $this->assertIsArray($users);

        $row = reset($users);

        $user = $this->usersModel->readUsersByIdDB(
            (new Users())
                ->setIdusers($row->idusers)
        );

        $data = $this->registrationModel->verifyAccountDB(
            (new Users())
                ->setUsersEmail($user->users_email)
        );

        $this->assertIsObject($data);
        $this->assertObjectHasProperty('idusers', $data);
        $this->assertObjectHasProperty('users_activation_code', $data);
        $this->assertSame($user->idusers, $data->idusers);
        $this->assertSame($user->users_activation_code, $data->users_activation_code);
    }

    public function testValidateAccountExistsDB(): void
    {
        $users = $this->usersModel->readUsersDB();

        $this->assertIsArray($users);

        $row = reset($users);

        $user = $this->usersModel->readUsersByIdDB(
            (new Users())
                ->setIdusers($row->idusers)
        );

        $cont = $this->registrationModel->validateAccountExistsDB(
            (new Users())
                ->setUsersEmail($user->users_email)
        );

        $this->assertIsObject($cont);
        $this->assertObjectHasProperty('cont', $cont);
        $this->assertSame(1, $cont->cont);
    }
}
