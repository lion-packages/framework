<?php

declare(strict_types=1);

namespace Tests\App\Http\Services\LionDatabase\MySQL;

use App\Exceptions\PasswordException;
use App\Exceptions\ProcessException;
use App\Http\Services\LionDatabase\MySQL\AuthenticatorService;
use App\Models\LionDatabase\MySQL\AuthenticatorModel;
use App\Models\LionDatabase\MySQL\UsersModel;
use Database\Class\Authenticator2FA;
use Database\Class\LionDatabase\MySQL\Users;
use Database\Factory\LionDatabase\MySQL\UsersFactory;
use Database\Migrations\LionDatabase\MySQL\Tables\DocumentTypes as DocumentTypesTable;
use Database\Migrations\LionDatabase\MySQL\Tables\Roles as RolesTable;
use Database\Migrations\LionDatabase\MySQL\Tables\Users as UsersTable;
use Database\Migrations\LionDatabase\MySQL\Views\ReadUsersById;
use Database\Seed\LionDatabase\MySQL\DocumentTypesSeed;
use Database\Seed\LionDatabase\MySQL\RolesSeed;
use Database\Seed\LionDatabase\MySQL\UsersSeed;
use Lion\Authentication\Auth2FA;
use Lion\Bundle\Test\Test;
use Lion\Exceptions\Exception;
use Lion\Request\Http;
use Lion\Request\Status;
use PHPUnit\Framework\Attributes\Test as Testing;
use PHPUnit\Framework\Attributes\TestWith;
use ReflectionException;
use stdClass;
use Tests\Providers\AuthJwtProviderTrait;

class AuthenticatorServiceTest extends Test
{
    use AuthJwtProviderTrait;

    private AuthenticatorService $authenticatorService;
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

        $this->authenticatorService = new AuthenticatorService();

        $this->usersModel = new UsersModel();
    }

    /**
     * @throws ReflectionException
     */
    #[Testing]
    public function setAuthenticatorModel(): void
    {
        $this->initReflection($this->authenticatorService);

        $this->assertInstanceOf(
            AuthenticatorService::class,
            $this->authenticatorService->setAuthenticatorModel(new AuthenticatorModel())
        );

        $this->assertInstanceOf(AuthenticatorModel::class, $this->getPrivateProperty('authenticatorModel'));
    }

    /**
     * @throws ReflectionException
     */
    #[Testing]
    public function setAuth2FA(): void
    {
        $this->initReflection($this->authenticatorService);

        $this->assertInstanceOf(
            AuthenticatorService::class,
            $this->authenticatorService->setAuth2FA(new Auth2FA())
        );

        $this->assertInstanceOf(Auth2FA::class, $this->getPrivateProperty('auth2FA'));
    }

    /**
     * @throws PasswordException
     */
    #[Testing]
    public function passwordVerify(): void
    {
        $users = $this->usersModel->readUsersDB();

        $this->assertIsArray($users);
        $this->assertCount(self::AVAILABLE_USERS, $users);

        $user = reset($users);

        $this->assertIsObject($user);
        $this->assertInstanceOf(stdClass::class, $user);
        $this->assertObjectHasProperty('idusers', $user);
        $this->assertIsInt($user->idusers);

        $capsule = (new Users())
            ->setIdusers($user->idusers)
            ->setUsersPassword(UsersFactory::USERS_PASSWORD);

        $this->authenticatorService
            ->setAuthenticatorModel(new AuthenticatorModel())
            ->passwordVerify($capsule);
    }

    /**
     * @throws Exception
     * @throws PasswordException
     */
    #[Testing]
    public function passwordVerifyIsError(): void
    {
        $this
            ->exception(PasswordException::class)
            ->exceptionMessage('password is invalid')
            ->exceptionStatus(Status::ERROR)
            ->exceptionCode(Http::INTERNAL_SERVER_ERROR)
            ->expectLionException(function (): void {
                $users = $this->usersModel->readUsersDB();

                $this->assertIsArray($users);
                $this->assertCount(self::AVAILABLE_USERS, $users);

                $user = reset($users);

                $this->assertIsObject($user);
                $this->assertInstanceOf(stdClass::class, $user);
                $this->assertObjectHasProperty('idusers', $user);
                $this->assertIsInt($user->idusers);

                $capsule = (new Users())
                    ->setIdusers($user->idusers)
                    ->setUsersPassword(fake()->numerify('#########'));

                $this->authenticatorService
                    ->setAuthenticatorModel(new AuthenticatorModel())
                    ->passwordVerify($capsule);
            });
    }

    /**
     * @throws Exception
     * @throws ProcessException
     */
    #[Testing]
    #[TestWith(['users_2fa' => UsersFactory::ENABLED_2FA, 'message' => '2FA security is active'])]
    #[TestWith(['users_2fa' => UsersFactory::DISABLED_2FA, 'message' => '2FA security is inactive'])]
    public function checkStatusIsError(int $users_2fa, string $message): void
    {
        $this
            ->exception(ProcessException::class)
            ->exceptionMessage($message)
            ->exceptionStatus(Status::ERROR)
            ->exceptionCode(Http::INTERNAL_SERVER_ERROR)
            ->expectLionException(function () use ($users_2fa): void {
                $users = $this->usersModel->readUsersDB();

                $this->assertIsArray($users);
                $this->assertCount(self::AVAILABLE_USERS, $users);

                $user = reset($users);

                $this->assertIsObject($user);
                $this->assertInstanceOf(stdClass::class, $user);
                $this->assertObjectHasProperty('idusers', $user);
                $this->assertIsInt($user->idusers);

                $this->authenticatorService
                    ->setAuthenticatorModel(new AuthenticatorModel());

                $capsule = (new Authenticator2FA())
                    ->setIdusers($user->idusers)
                    ->setUsers2fa($users_2fa);

                $this->authenticatorService->update2FA($capsule);

                /** @var stdClass $user */
                $user = $this->usersModel->readUsersByIdDB(
                    (new Users())
                        ->setIdusers($user->idusers)
                );

                $this->assertIsObject($user);
                $this->assertInstanceOf(stdClass::class, $user);
                $this->assertObjectHasProperty('users_2fa', $user);
                $this->assertIsInt($user->users_2fa);
                $this->assertSame($users_2fa, $user->users_2fa);

                $this->authenticatorService->checkStatus($users_2fa, $capsule);
            });
    }

    /**
     * @throws Exception
     */
    #[Testing]
    public function verify2FAIsError(): void
    {
        $this
            ->exception(ProcessException::class)
            ->exceptionMessage('failed to authenticate, the code is not valid')
            ->exceptionStatus(Status::ERROR)
            ->exceptionCode(Http::FORBIDDEN)
            ->expectLionException(function (): void {
                $qr = (new Auth2FA())->qr(env('APP_NAME'), env('MAIL_USER_NAME'));

                $this->authenticatorService
                    ->setAuth2FA(new Auth2FA())
                    ->verify2FA(
                        $qr->data->secretKey,
                        (new Authenticator2FA())
                            ->setUsersSecretCode('000000')
                    );
            });
    }

    /**
     * @throws ProcessException
     */
    #[Testing]
    public function update2FA(): void
    {
        $users = $this->usersModel->readUsersDB();

        $this->assertIsArray($users);
        $this->assertCount(self::AVAILABLE_USERS, $users);

        $user = reset($users);

        $this->assertIsObject($user);
        $this->assertInstanceOf(stdClass::class, $user);
        $this->assertObjectHasProperty('idusers', $user);
        $this->assertIsInt($user->idusers);

        $qr = (new Auth2FA())->qr(env('APP_NAME'), env('MAIL_USER_NAME'));

        $this->authenticatorService
            ->setAuthenticatorModel(new AuthenticatorModel())
            ->update2FA(
                (new Authenticator2FA())
                    ->setIdusers($user->idusers)
                    ->setUsers2fa(UsersFactory::ENABLED_2FA)
                    ->setUsers2faSecret($qr->data->secretKey)
            );

        /** @var stdClass $user */
        $user = $this->usersModel->readUsersByIdDB(
            (new Users())
                ->setIdusers($user->idusers)
        );

        $this->assertIsObject($user);
        $this->assertInstanceOf(stdClass::class, $user);
        $this->assertObjectHasProperty('users_2fa', $user);
        $this->assertObjectHasProperty('users_2fa_secret', $user);
        $this->assertIsInt($user->users_2fa);
        $this->assertSame(UsersFactory::ENABLED_2FA, $user->users_2fa);
        $this->assertIsString($user->users_2fa_secret);
        $this->assertSame($qr->data->secretKey, $user->users_2fa_secret);
    }

    /**
     * @throws Exception
     */
    #[Testing]
    public function update2FAIsError(): void
    {
        $this
            ->exception(ProcessException::class)
            ->exceptionMessage('an error occurred while enabling 2-step security with 2FA')
            ->exceptionStatus(Status::ERROR)
            ->exceptionCode(Http::INTERNAL_SERVER_ERROR)
            ->expectLionException(function (): void {
                $users = $this->usersModel->readUsersDB();

                $this->assertIsArray($users);
                $this->assertCount(self::AVAILABLE_USERS, $users);

                $user = reset($users);

                $this->assertIsObject($user);
                $this->assertInstanceOf(stdClass::class, $user);
                $this->assertObjectHasProperty('idusers', $user);
                $this->assertIsInt($user->idusers);

                $qr = (new Auth2FA())->qr(env('APP_NAME'), env('MAIL_USER_NAME'));

                $this->authenticatorService
                    ->setAuthenticatorModel(new AuthenticatorModel())
                    ->update2FA(
                        (new Authenticator2FA())
                            ->setIdusers($user->idusers)
                            ->setUsers2fa(UsersFactory::ENABLED_2FA)
                            ->setUsers2faSecret($qr->data->secretKey . fake()->numerify('####'))
                    );
            });
    }
}
