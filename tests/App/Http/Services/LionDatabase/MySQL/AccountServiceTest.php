<?php

declare(strict_types=1);

namespace Tests\App\Http\Services\LionDatabase\MySQL;

use App\Exceptions\AccountException;
use App\Html\Email\RecoveryAccountHtml;
use App\Http\Services\LionDatabase\MySQL\AccountService;
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

    public function testSendVerificationCode(): void
    {
        $account = fake()->email();

        $code = fake()->numerify('######');

        $this->accountService->sendRecoveryCode(
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
        $users = (new Users())
            ->setUsersEmail(self::USERS_EMAIL);

        $user = (new UsersModel())->readUsersByEmailDB($users);

        $code = fake()->numerify('######');

        $users
            ->setIdusers($user->idusers)
            ->setUsersRecoveryCode($code);

        $this->accountService->updateRecoveryCode($users);

        $user = (new UsersModel())->readUsersByEmailDB($users);

        $this->assertSame($code, $user->users_recovery_code);
    }
}
