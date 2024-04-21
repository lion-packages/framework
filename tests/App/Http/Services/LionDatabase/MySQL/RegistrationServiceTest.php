<?php

declare(strict_types=1);

namespace Tests\App\Http\Services\LionDatabase\MySQL;

use App\Exceptions\AuthenticationException;
use App\Html\Email\VerifyAccountHtml;
use App\Http\Services\LionDatabase\MySQL\RegistrationService;
use Database\Class\LionDatabase\MySQL\Users;
use Lion\Bundle\Enums\TaskStatusEnum;
use Lion\Database\Drivers\MySQL as DB;
use Lion\Database\Drivers\Schema\MySQL as Schema;
use Lion\Dependency\Injection\Container;
use Lion\Request\Request;
use Lion\Test\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Providers\App\Http\Services\LionDatabase\MySQL\RegistrationServiceProviderTrait;
use Tests\Providers\SetUpMigrationsAndQueuesProviderTrait;

class RegistrationServiceTest extends Test
{
    use RegistrationServiceProviderTrait;
    use SetUpMigrationsAndQueuesProviderTrait;

    private RegistrationService $registrationService;

    protected function setUp(): void
    {
        $this->runMigrationsAndQueues();

        $this->registrationService = (new Container())->injectDependencies(new RegistrationService());
    }

    protected function tearDown(): void
    {
        Schema::truncateTable('task_queue')->execute();
    }

    public function testSendVerifiyEmail(): void
    {
        $account = fake()->email();

        $code = fake()->numerify('######');

        $this->registrationService->sendVerifiyEmail(
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

    #[DataProvider('verifyAccountProvider')]
    public function testVerifyAccount(string $message, object $data, Users $users): void
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionCode(Request::HTTP_FORBIDDEN);
        $this->expectExceptionMessage($message);

        $this->registrationService->verifyAccount($users, $data);
    }
}
