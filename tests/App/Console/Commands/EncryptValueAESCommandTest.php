<?php

declare(strict_types=1);

namespace Tests\App\Console\Commands;

use App\Console\Commands\EncryptValueAESCommand;
use App\Http\Services\AESService;
use Lion\Security\AES;
use Lion\Test\Test;
use PHPUnit\Framework\Attributes\Test as Testing;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class EncryptValueAESCommandTest extends Test
{
    private CommandTester $commandTester;
    private AESService $aESService;

    protected function setUp(): void
    {
        $this->aESService = (new AESService())
            ->setAES(new AES());

        $command = (new EncryptValueAESCommand())
            ->setAESService($this->aESService);

        $application = new Application();

        $application->add($command);

        $this->commandTester = new CommandTester($application->find('aes:encode'));
    }

    #[Testing]
    public function execute(): void
    {
        $code = uniqid();

        $this->assertSame(Command::SUCCESS, $this->commandTester->setInputs([$code])->execute([]));

        $encode = $this->aESService->encode([
            'code' => $code,
        ]);

        $this->assertStringContainsString($encode['code'], $this->commandTester->getDisplay());
    }

    #[Testing]
    public function executeWithEmptyValue(): void
    {
        $this->assertSame(Command::INVALID, $this->commandTester->setInputs([""])->execute([]));
        $this->assertStringContainsString('you must enter a value for encryption', $this->commandTester->getDisplay());
    }
}
