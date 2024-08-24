<?php

declare(strict_types=1);

namespace Tests\App\Http\Services\LionDatabase\MySQL;

use App\Exceptions\AccountException;
use App\Http\Services\LionDatabase\MySQL\AccountService;
use App\Models\LionDatabase\MySQL\RegistrationModel;
use App\Models\LionDatabase\MySQL\UsersModel;
use Database\Class\LionDatabase\MySQL\Users;
use Exception as ExceptionGlobal;
use Lion\Bundle\Enums\TaskStatusEnum;
use Lion\Database\Drivers\MySQL as DB;
use Lion\Exceptions\Exception;
use Lion\Request\Http;
use Lion\Request\Status;
use Lion\Test\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test as Testing;
use stdClass;
use Tests\Providers\App\Http\Services\LionDatabase\MySQL\AccountServiceProviderTrait;
use Tests\Providers\SetUpMigrationsAndQueuesProviderTrait;

class AccountServiceTest extends Test
{
    use AccountServiceProviderTrait;
    use SetUpMigrationsAndQueuesProviderTrait;

    const string USERS_EMAIL = 'root@dev.com';

    private AccountService $accountService;

    protected function setUp(): void
    {
        $this->runMigrations();

        $this->accountService = (new AccountService())
            ->setUsersModel(new UsersModel());
    }

    /**
     * @throws AccountException
     * @throws Exception
     */
    #[Testing]
    public function checkRecoveryCodeInactive(): void
    {
        $this
            ->exception(AccountException::class)
            ->exceptionMessage('a verification code has already been sent to this account')
            ->exceptionStatus(Status::ERROR)
            ->exceptionCode(Http::FORBIDDEN)
            ->expectLionException(function (): void {
                $this->accountService->checkRecoveryCodeInactive(
                    (new Users())
                        ->setUsersRecoveryCode(fake()->numerify('######'))
                );
            });
    }

    /**
     * @throws ExceptionGlobal
     */
    #[Testing]
    public function sendVerifyCodeEmail(): void
    {
        $account = fake()->email();

        $code = fake()->numerify('######');

        $this->accountService->sendVerifyCodeEmail(
            (new Users())
                ->setUsersEmail($account)
                ->setUsersActivationCode($code)
        );

        $taskQueue = DB::table('task_queue')
            ->select()
            ->getAll();

        $this->assertIsArray($taskQueue);

        $row = reset($taskQueue);

        $this->assertIsObject($row);
        $this->assertInstanceOf(stdClass::class, $row);
        $this->assertObjectHasProperty('task_queue_data', $row);
        $this->assertObjectHasProperty('task_queue_status', $row);
        $this->assertIsString($row->task_queue_data);
        $this->assertIsString($row->task_queue_status);
        $this->assertSame(TaskStatusEnum::PENDING->value, $row->task_queue_status);

        $this->assertJsonContent($row->task_queue_data, [
            'code' => $code,
            'account' => $account,
        ]);
    }

    /**
     * @throws ExceptionGlobal
     */
    #[Testing]
    public function sendRecoveryCodeEmail(): void
    {
        $account = fake()->email();

        $code = fake()->numerify('######');

        $this->accountService->sendRecoveryCodeEmail(
            (new Users())
                ->setUsersEmail($account)
                ->setUsersRecoveryCode($code)
        );

        $taskQueue = DB::table('task_queue')
            ->select()
            ->getAll();

        $this->assertIsArray($taskQueue);

        $row = reset($taskQueue);

        $this->assertIsObject($row);
        $this->assertInstanceOf(stdClass::class, $row);
        $this->assertObjectHasProperty('task_queue_data', $row);
        $this->assertObjectHasProperty('task_queue_status', $row);
        $this->assertIsString($row->task_queue_data);
        $this->assertIsString($row->task_queue_status);
        $this->assertSame(TaskStatusEnum::PENDING->value, $row->task_queue_status);

        $this->assertJsonContent($row->task_queue_data, [
            'code' => $code,
            'account' => $account,
        ]);
    }

    /**
     * @throws AccountException
     * @throws Exception
     */
    #[Testing]
    #[DataProvider('verifyRecoveryCodeProvider')]
    public function verifyRecoveryCode(Users $users, object $data, string $exceptionMessage): void
    {
        $this
            ->exception(AccountException::class)
            ->exceptionMessage($exceptionMessage)
            ->exceptionStatus(Status::ERROR)
            ->exceptionCode(Http::FORBIDDEN)
            ->expectLionException(function () use ($users, $data): void {
                $this->accountService->verifyRecoveryCode($users, $data);
            });
    }

    /**
     * @throws AccountException
     * @throws Exception
     */
    #[Testing]
    #[DataProvider('verifyActivationCodeProvider')]
    public function verifyActivationCode(Users $users, object $data, string $exceptionMessage): void
    {
        $this
            ->exception(AccountException::class)
            ->exceptionMessage($exceptionMessage)
            ->exceptionStatus(Status::ERROR)
            ->exceptionCode(Http::FORBIDDEN)
            ->expectLionException(function () use ($users, $data): void {
                $this->accountService->verifyActivationCode($users, $data);
            });
    }

    /**
     * @throws AccountException
     * @throws Exception
     */
    #[Testing]
    public function updateRecoveryCode(): void
    {
        $this
            ->exception(AccountException::class)
            ->exceptionMessage('verification code is invalid [ERR-3]')
            ->exceptionStatus(Status::ERROR)
            ->exceptionCode(Http::UNAUTHORIZED)
            ->expectLionException(function (): void {
                $users = (new Users())
                    ->setUsersEmail(self::USERS_EMAIL);

                /** @var stdClass $user */
                $user = (new UsersModel())->readUsersByEmailDB($users);

                $code = fake()->numerify('##########');

                $users
                    ->setIdusers($user->idusers)
                    ->setUsersRecoveryCode($code);

                $this->accountService->updateRecoveryCode($users);
            });
    }

    /**
     * @throws AccountException
     * @throws Exception
     */
    #[Testing]
    public function updateActivationCode(): void
    {
        $this
            ->exception(AccountException::class)
            ->exceptionMessage('verification code is invalid [ERR-3]')
            ->exceptionStatus(Status::ERROR)
            ->exceptionCode(Http::UNAUTHORIZED)
            ->expectLionException(function (): void {
                $users = (new Users())
                    ->setUsersEmail(self::USERS_EMAIL);

                /** @var stdClass $user */
                $user = (new UsersModel())->readUsersByEmailDB($users);

                $users
                    ->setIdusers($user->idusers)
                    ->setUsersActivationCode(fake()->numerify('##########'));

                $this->accountService->updateActivationCode($users);
            });
    }

    /**
     * @throws AccountException
     * @throws Exception
     */
    #[Testing]
    public function validateAccountExists(): void
    {
        $this
            ->exception(AccountException::class)
            ->exceptionMessage('there is already an account registered with this email')
            ->exceptionStatus(Status::ERROR)
            ->exceptionCode(Http::BAD_REQUEST)
            ->expectLionException(function (): void {
                $this->accountService->validateAccountExists(
                    new RegistrationModel(),
                    (new Users())
                        ->setUsersEmail(self::USERS_EMAIL)
                );
            });
    }
}
