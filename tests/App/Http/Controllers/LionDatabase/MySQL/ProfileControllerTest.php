<?php

declare(strict_types=1);

namespace Tests\App\Http\Controllers\LionDatabase\MySQL;

use App\Exceptions\ProcessException;
use App\Http\Controllers\LionDatabase\MySQL\ProfileController;
use App\Http\Services\AESService;
use App\Http\Services\JWTService;
use App\Models\LionDatabase\MySQL\ProfileModel;
use App\Models\LionDatabase\MySQL\UsersModel;
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
use Lion\Exceptions\Exception;
use Lion\Request\Http;
use Lion\Request\Status;
use Lion\Security\AES;
use Lion\Security\JWT;
use Lion\Security\RSA;
use PHPUnit\Framework\Attributes\Test as Testing;
use stdClass;
use Tests\Providers\AuthJwtProviderTrait;

class ProfileControllerTest extends Test
{
    use AuthJwtProviderTrait;

    private const string USERS_EMAIL = 'root@dev.com';

    private ProfileController $profileController;
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

        $this->profileController = new ProfileController();

        $this->usersModel = new UsersModel();
    }

    protected function tearDown(): void
    {
        $this->assertHeaderNotHasKey('HTTP_AUTHORIZATION');
    }

    #[Testing]
    public function readProfile(): void
    {
        /** @var stdClass $user */
        $user = $this->usersModel->readUsersByEmailDB(
            (new Users())
                ->setUsersEmail(UsersFactory::USERS_EMAIL)
        );

        $this->assertIsObject($user);
        $this->assertInstanceOf(stdclass::class, $user);
        $this->assertObjectHasProperty('idusers', $user);
        $this->assertIsInt($user->idusers);

        $encode = $this->AESEncode([
            'idusers' => (string) $user->idusers,
        ]);

        $_SERVER['HTTP_AUTHORIZATION'] = $this->getAuthorization([
            'idusers' => $encode['idusers']
        ]);

        /** @var stdClass $response */
        $response = $this->profileController->readProfile(
            new Users(),
            new ProfileModel(),
            (new JWTService())
                ->setRSA(new RSA())
                ->setJWT(new JWT()),
            (new AESService())
                ->setAES(new AES())
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
        $this->assertSame($user->idusers, $response->idusers);
    }

    /**
     * @throws ProcessException
     */
    #[Testing]
    public function updateProfile(): void
    {
        /** @var stdClass $user */
        $user = $this->usersModel->readUsersByEmailDB(
            (new Users())
                ->setUsersEmail(UsersFactory::USERS_EMAIL)
        );

        $this->assertIsObject($user);
        $this->assertInstanceOf(stdclass::class, $user);
        $this->assertObjectHasProperty('idusers', $user);
        $this->assertIsInt($user->idusers);

        $encode = $this->AESEncode([
            'idusers' => (string) $user->idusers,
        ]);

        $_SERVER['HTTP_AUTHORIZATION'] = $this->getAuthorization([
            'idusers' => $encode['idusers'],
        ]);

        $users_name = fake()->name();

        $_POST['users_name'] = $users_name;

        $response = $this->profileController->updateProfile(
            new Users(),
            new ProfileModel(),
            (new JWTService())
                ->setRSA(new RSA())
                ->setJWT(new JWT()),
            (new AESService())
                ->setAES(new AES())
        );

        $this->assertIsObject($response);
        $this->assertInstanceOf(stdClass::class, $response);
        $this->assertObjectHasProperty('code', $response);
        $this->assertObjectHasProperty('status', $response);
        $this->assertObjectHasProperty('message', $response);
        $this->assertIsInt($response->code);
        $this->assertIsString($response->status);
        $this->assertIsString($response->message);
        $this->assertSame(Http::OK, $response->code);
        $this->assertSame(Status::SUCCESS, $response->status);
        $this->assertSame('profile updated successfully', $response->message);
        $this->assertHttpBodyNotHasKey('users_name');

        /** @var stdClass $user */
        $user = $this->usersModel->readUsersByEmailDB(
            (new Users())
                ->setUsersEmail(UsersFactory::USERS_EMAIL)
        );

        $this->assertIsObject($user);
        $this->assertInstanceOf(stdclass::class, $user);
        $this->assertObjectHasProperty('users_name', $user);
        $this->assertSame($users_name, $user->users_name);
    }

    /**
     * @throws Exception
     * @throws ProcessException
     */
    #[Testing]
    public function updateProfileIsError(): void
    {
        $this
            ->exception(ProcessException::class)
            ->exceptionCode(Http::INTERNAL_SERVER_ERROR)
            ->exceptionStatus(Status::ERROR)
            ->exceptionMessage("an error occurred while updating the user's profile")
            ->expectLionException(function (): void {
                $encode = $this->AESEncode([
                    'idusers' => fake()->numerify('###############'),
                ]);

                $_SERVER['HTTP_AUTHORIZATION'] = $this->getAuthorization([
                    'idusers' => $encode['idusers'],
                ]);

                $this->profileController->updateProfile(
                    new Users(),
                    new ProfileModel(),
                    (new JWTService())
                        ->setRSA(new RSA())
                        ->setJWT(new JWT()),
                    (new AESService())
                        ->setAES(new AES())
                );
            });
    }
}
