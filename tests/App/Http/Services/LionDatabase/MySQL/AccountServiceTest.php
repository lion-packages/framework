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
use Lion\Bundle\Enums\TaskStatusEnum;
use Lion\Database\Drivers\MySQL as DB;
use Lion\Database\Drivers\Schema\MySQL as Schema;
use Lion\Dependency\Injection\Container;
use Lion\Request\Request;
use Lion\Test\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Providers\App\Http\Services\LionDatabase\MySQL\AccountServiceProviderTrait;
use Tests\Providers\SetUpMigrationsAndQueuesProviderTrait;

class AccountServiceTest extends Test
{
    use AccountServiceProviderTrait;
    use SetUpMigrationsAndQueuesProviderTrait;

    const USERS_EMAIL = 'root@dev.com';

    private AccountService $accountService;

    protected function setUp(): void
    {
        $this->runMigrationsAndQueues();

        $this->accountService = (new Container)
            ->injectDependencies(new AccountService());
    }

    protected function tearDown(): void
    {
        Schema::truncateTable('users')->execute();

        Schema::truncateTable('task_queue')->execute();
    }

    public function testCheckRecoveryCodeInactive(): void
    {
        $this->expectException(AccountException::class);
        $this->expectExceptionCode(Request::HTTP_FORBIDDEN);
        $this->expectExceptionMessage('a verification code has already been sent to this account');

        $this->accountService->checkRecoveryCodeInactive(
            (new Users())
                ->setUsersRecoveryCode(fake()->numerify('######'))
        );
    }

    public function testSendVerifiyCodeEmail(): void
    {
        $account = fake()->email();

        $code = fake()->numerify('######');

        $this->accountService->sendVerifiyCodeEmail(
            (new Users())
                ->setUsersEmail($account)
                ->setUsersActivationCode($code)
        );

        $taskQueue = DB::table('task_queue')->select()->getAll();

        $this->assertIsArray($taskQueue);

        $row = reset($taskQueue);

        $this->assertIsObject($row);
        $this->assertObjectHasProperty('task_queue_data', $row);
        $this->assertObjectHasProperty('task_queue_status', $row);
        $this->assertSame(TaskStatusEnum::PENDING->value, $row->task_queue_status);
        $this->assertIsString($row->task_queue_data);

        $this->assertJsonContent($row->task_queue_data, [
            'code' => $code,
            'account' => $account,
            'template' => VerifyAccountHtml::class
        ]);
    }

    public function testSendRecoveryCodeEmail(): void
    {
        $account = fake()->email();

        $code = fake()->numerify('######');

        $this->accountService->sendRecoveryCodeEmail(
            (new Users())
                ->setUsersEmail($account)
                ->setUsersRecoveryCode($code)
        );

        $taskQueue = DB::table('task_queue')->select()->getAll();

        $this->assertIsArray($taskQueue);

        $row = reset($taskQueue);

        $this->assertIsObject($row);
        $this->assertObjectHasProperty('task_queue_data', $row);
        $this->assertObjectHasProperty('task_queue_status', $row);
        $this->assertSame(TaskStatusEnum::PENDING->value, $row->task_queue_status);
        $this->assertIsString($row->task_queue_data);

        $this->assertJsonContent($row->task_queue_data, [
            'code' => $code,
            'account' => $account,
            'template' => RecoveryAccountHtml::class,
        ]);
    }

    #[DataProvider('verifyRecoveryCodeProvider')]
    public function testVerifyRecoveryCode(Users $users, object $data, string $exceptionMessage): void
    {
        $this->expectException(AccountException::class);
        $this->expectExceptionCode(Request::HTTP_FORBIDDEN);
        $this->expectExceptionMessage($exceptionMessage);

        $this->accountService->verifyRecoveryCode($users, $data);
    }

    #[DataProvider('verifyActivationCodeProvider')]
    public function testVerifyActivationCode(Users $users, object $data, string $exceptionMessage): void
    {
        $this->expectException(AccountException::class);
        $this->expectExceptionCode(Request::HTTP_FORBIDDEN);
        $this->expectExceptionMessage($exceptionMessage);

        $this->accountService->verifyActivationCode($users, $data);
    }

    public function testUpdateRecoveryCode(): void
    {
        $this->expectException(AccountException::class);
        $this->expectExceptionCode(Request::HTTP_UNAUTHORIZED);
        $this->expectExceptionMessage('verification code is invalid [ERR-3]');

        $users = (new Users())
            ->setUsersEmail(self::USERS_EMAIL);

        $user = (new UsersModel())->readUsersByEmailDB($users);

        $code = fake()->numerify('##########');

        $users
            ->setIdusers($user->idusers)
            ->setUsersRecoveryCode($code);

        $this->accountService->updateRecoveryCode($users);
    }

    public function testUpdateActivationCode(): void
    {
        $this->expectException(AccountException::class);
        $this->expectExceptionCode(Request::HTTP_UNAUTHORIZED);
        $this->expectExceptionMessage('verification code is invalid [ERR-3]');

        $users = (new Users())
            ->setUsersEmail(self::USERS_EMAIL);

        $user = (new UsersModel())->readUsersByEmailDB($users);

        $code = fake()->numerify('##########');

        $users
            ->setIdusers($user->idusers)
            ->setUsersActivationCode($code);

        $this->accountService->updateActivationCode($users);
    }

    public function testValidateAccountExists(): void
    {
        $this->expectException(AccountException::class);
        $this->expectExceptionCode(Request::HTTP_BAD_REQUEST);
        $this->expectExceptionMessage('there is already an account registered with this email');

        $this->accountService->validateAccountExists(
            new RegistrationModel(),
            (new Users())
                ->setUsersEmail(self::USERS_EMAIL)
        );
    }
}
