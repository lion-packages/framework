<?php

declare(strict_types=1);

namespace Tests\App\Http\Services\LionDatabase\MySQL;

use App\Exceptions\AccountException;
use App\Html\Email\RecoveryAccountHtml;
use App\Html\Email\VerifyAccountHtml;
use App\Http\Services\LionDatabase\MySQL\AccountService;
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
use Exception as ExceptionGlobal;
use Lion\Bundle\Helpers\Commands\Schedule\Task;
use Lion\Bundle\Helpers\Commands\Schedule\TaskQueue;
use Lion\Bundle\Helpers\Redis;
use Lion\Bundle\Test\Test;
use Lion\Exceptions\Exception;
use Lion\Request\Http;
use Lion\Request\Status;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test as Testing;
use stdClass;
use Tests\Providers\App\Http\Services\LionDatabase\MySQL\AccountServiceProviderTrait;

class AccountServiceTest extends Test
{
    use AccountServiceProviderTrait;

    const string USERS_EMAIL = 'root@dev.com';

    private AccountService $accountService;
    private TaskQueue $taskQueue;

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

        $this->accountService = (new AccountService())
            ->setUsersModel(new UsersModel());

        $this->taskQueue = (new TaskQueue())
            ->setRedis(new Redis());
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
    public function runSendVerificationCodeEmail(): void
    {
        $account = fake()->email();

        $code = fake()->numerify('######');

        $this->taskQueue->push(
            new Task(AccountService::class, 'runSendVerificationCodeEmail', [
                'account' => $account,
                'code' => $code
            ])
        );

        $task = $this->taskQueue->get();

        $this->assertIsString($task);

        $taskRow = json_decode($task, true);

        $this->assertIsArray($taskRow);
        $this->assertNotEmpty($taskRow);
        $this->assertArrayHasKey('account', $taskRow['data']);
        $this->assertArrayHasKey('code', $taskRow['data']);

        $response = $this->accountService->runSendVerificationCodeEmail(
            new RecoveryAccountHtml(),
            $taskRow,
            $account,
            $code
        );

        $this->assertIsBool($response);
        $this->assertTrue($response);
    }

    /**
     * @throws ExceptionGlobal
     */
    #[Testing]
    public function runSendRecoveryCodeByEmail(): void
    {
        $account = fake()->email();

        $code = fake()->numerify('######');

        $this->taskQueue->push(
            new Task(AccountService::class, 'runSendRecoveryCodeByEmail', [
                'account' => $account,
                'code' => $code,
            ])
        );

        $task = $this->taskQueue->get();

        $this->assertIsString($task);

        $taskRow = json_decode($task, true);

        $this->assertIsArray($taskRow);
        $this->assertNotEmpty($taskRow);
        $this->assertArrayHasKey('account', $taskRow['data']);
        $this->assertArrayHasKey('code', $taskRow['data']);

        $response = $this->accountService->runSendRecoveryCodeByEmail(
            new VerifyAccountHtml(),
            $taskRow,
            $account,
            $code
        );

        $this->assertIsBool($response);
        $this->assertTrue($response);
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
