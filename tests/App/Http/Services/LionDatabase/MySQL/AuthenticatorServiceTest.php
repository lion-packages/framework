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
use Lion\Authentication\Auth2FA;
use Lion\Database\Drivers\Schema\MySQL as Schema;
use Lion\Request\Http;
use Lion\Request\Status;
use Lion\Test\Test;
use PHPUnit\Framework\Attributes\Test as Testing;
use PHPUnit\Framework\Attributes\TestWith;
use PragmaRX\Google2FAQRCode\Google2FA;
use Tests\Providers\AuthJwtProviderTrait;
use Tests\Providers\SetUpMigrationsAndQueuesProviderTrait;

class AuthenticatorServiceTest extends Test
{
    use AuthJwtProviderTrait;
    use SetUpMigrationsAndQueuesProviderTrait;

    private AuthenticatorService $authenticatorService;
    private UsersModel $usersModel;

    protected function setUp(): void
    {
        $this->runMigrationsAndQueues();

        $this->authenticatorService = new AuthenticatorService();

        $this->usersModel = new UsersModel();
    }

    protected function tearDown(): void
    {
        Schema::truncateTable('users')->execute();
    }

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

    #[Testing]
    public function passwordVerify(): void
    {
        $users = $this->usersModel->readUsersDB();

        $this->assertIsArray($users);
        $this->assertCount(self::AVAILABLE_USERS, $users);

        $user = reset($users);

        $this->assertIsObject($user);
        $this->assertObjectHasProperty('idusers', $user);

        $capsule = (new Users())
            ->setIdusers($user->idusers)
            ->setUsersPassword(UsersFactory::USERS_PASSWORD);

        $this->authenticatorService
            ->setAuthenticatorModel(new AuthenticatorModel())
            ->passwordVerify($capsule);
    }

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
                $this->assertObjectHasProperty('idusers', $user);

                $capsule = (new Users())
                    ->setIdusers($user->idusers)
                    ->setUsersPassword(fake()->numerify('#########'));

                $this->authenticatorService
                    ->setAuthenticatorModel(new AuthenticatorModel())
                    ->passwordVerify($capsule);
            });
    }

    #[Testing]
    #[TestWith(['users_2fa' => UsersFactory::ENABLED_2FA, 'users_2fa_update' => UsersFactory::DISABLED_2FA])]
    #[TestWith(['users_2fa' => UsersFactory::DISABLED_2FA, 'users_2fa_update' => UsersFactory::ENABLED_2FA])]
    public function checkStatus(int $users_2fa, int $users_2fa_update): void
    {
        $users = $this->usersModel->readUsersDB();

        $this->assertIsArray($users);
        $this->assertCount(self::AVAILABLE_USERS, $users);

        $user = reset($users);

        $this->assertIsObject($user);
        $this->assertObjectHasProperty('idusers', $user);

        $this->authenticatorService->setAuthenticatorModel(new AuthenticatorModel());

        $capsule = (new Authenticator2FA())
            ->setIdusers($user->idusers)
            ->setUsers2fa($users_2fa_update);

        $this->authenticatorService->update2FA($capsule);

        $user = $this->usersModel->readUsersByIdDB(
            (new Users())
                ->setIdusers($user->idusers)
        );

        $this->assertIsObject($user);
        $this->assertObjectHasProperty('users_2fa', $user);
        $this->assertSame($users_2fa_update, $user->users_2fa);

        $this->authenticatorService->checkStatus($users_2fa, $capsule);
    }

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
                $this->assertObjectHasProperty('idusers', $user);

                $this->authenticatorService
                    ->setAuthenticatorModel(new AuthenticatorModel());

                $capsule = (new Authenticator2FA())
                    ->setIdusers($user->idusers)
                    ->setUsers2fa($users_2fa);

                $this->authenticatorService->update2FA($capsule);

                $user = $this->usersModel->readUsersByIdDB(
                    (new Users())
                        ->setIdusers($user->idusers)
                );

                $this->assertIsObject($user);
                $this->assertObjectHasProperty('users_2fa', $user);
                $this->assertSame($users_2fa, $user->users_2fa);

                $this->authenticatorService->checkStatus($users_2fa, $capsule);
            });
    }

    #[Testing]
    public function verify2FA(): void
    {
        $qr = (new Auth2FA())->qr(env('APP_NAME'), env('MAIL_USER_NAME'));

        $this->assertIsObject($qr);
        $this->assertObjectHasProperty('status', $qr);
        $this->assertObjectHasProperty('message', $qr);
        $this->assertObjectHasProperty('data', $qr);
        $this->assertObjectHasProperty('secretKey', $qr->data);
        $this->assertSame(Status::SUCCESS, $qr->status);
        $this->assertIsString($qr->data->secretKey);

        $code = (new Google2FA())->getCurrentOtp($qr->data->secretKey);

        $this->assertIsString($code);

        $this->authenticatorService
            ->setAuth2FA(new Auth2FA())
            ->verify2FA(
                $qr->data->secretKey,
                (new Authenticator2FA())
                    ->setUsersSecretCode($code)
            );
    }

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

                $this->assertIsObject($qr);
                $this->assertObjectHasProperty('status', $qr);
                $this->assertObjectHasProperty('message', $qr);
                $this->assertObjectHasProperty('data', $qr);
                $this->assertObjectHasProperty('secretKey', $qr->data);
                $this->assertSame(Status::SUCCESS, $qr->status);
                $this->assertIsString($qr->data->secretKey);

                $this->authenticatorService
                    ->setAuth2FA(new Auth2FA())
                    ->verify2FA(
                        $qr->data->secretKey,
                        (new Authenticator2FA())
                            ->setUsersSecretCode('000000')
                    );
            });
    }

    #[Testing]
    public function update2FA(): void
    {
        $users = $this->usersModel->readUsersDB();

        $this->assertIsArray($users);
        $this->assertCount(self::AVAILABLE_USERS, $users);

        $user = reset($users);

        $this->assertIsObject($user);
        $this->assertObjectHasProperty('idusers', $user);

        $qr = (new Auth2FA())->qr(env('APP_NAME'), env('MAIL_USER_NAME'));

        $this->assertIsObject($qr);
        $this->assertObjectHasProperty('status', $qr);
        $this->assertObjectHasProperty('message', $qr);
        $this->assertObjectHasProperty('data', $qr);
        $this->assertObjectHasProperty('secretKey', $qr->data);
        $this->assertSame(Status::SUCCESS, $qr->status);
        $this->assertIsString($qr->data->secretKey);

        $this->authenticatorService
            ->setAuthenticatorModel(new AuthenticatorModel())
            ->update2FA(
                (new Authenticator2FA())
                    ->setIdusers($user->idusers)
                    ->setUsers2fa(UsersFactory::ENABLED_2FA)
                    ->setUsers2faSecret($qr->data->secretKey)
            );

        $user = $this->usersModel->readUsersByIdDB(
            (new Users())
                ->setIdusers($user->idusers)
        );

        $this->assertObjectHasProperty('users_2fa', $user);
        $this->assertObjectHasProperty('users_2fa_secret', $user);
        $this->assertSame(UsersFactory::ENABLED_2FA, $user->users_2fa);
        $this->assertSame($qr->data->secretKey, $user->users_2fa_secret);
    }

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
                $this->assertObjectHasProperty('idusers', $user);

                $qr = (new Auth2FA())->qr(env('APP_NAME'), env('MAIL_USER_NAME'));

                $this->assertIsObject($qr);
                $this->assertObjectHasProperty('status', $qr);
                $this->assertObjectHasProperty('message', $qr);
                $this->assertObjectHasProperty('data', $qr);
                $this->assertObjectHasProperty('secretKey', $qr->data);
                $this->assertSame(Status::SUCCESS, $qr->status);
                $this->assertIsString($qr->data->secretKey);

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
