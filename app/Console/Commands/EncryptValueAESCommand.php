<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Http\Services\AESService;
use DI\Attribute\Inject;
use Lion\Command\Command;
use Lion\Security\Exceptions\AESException;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * It is responsible for encrypting data with AES
 *
 * @property AESService $aESService
 *
 * @package App\Console\Commands
 */
class EncryptValueAESCommand extends Command
{
    /**
     * [Encrypt and decrypt data with AES]
     *
     * @var AESService $aESService
     */
    private AESService $aESService;

    #[Inject]
    public function setAESService(AESService $aESService): EncryptValueAESCommand
    {
        $this->aESService = $aESService;

        return $this;
    }

    /**
     * Configures the current command
     *
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setName('aes:encode')
            ->setDescription('Encrypt data with AES');
    }

    /**
     * Executes the current command
     *
     * This method is not abstract because you can use this class
     * as a concrete class. In this case, instead of defining the
     * execute() method, you set the code to execute by passing
     * a Closure to the setCode() method
     *
     * @param InputInterface $input [InputInterface is the interface implemented
     * by all input classes]
     * @param OutputInterface $output [OutputInterface is the interface
     * implemented by all Output classes]
     *
     * @return int
     *
     * @throws LogicException [When this abstract method is not implemented]
     * @throws AESException [If encryption fails]
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');

        $value = $helper->ask($input, $output, new Question($this->warningOutput("\t>> enter a value: "), NULL_VALUE));

        if (NULL_VALUE === $value) {
            $output->writeln($this->errorOutput("\t>> you must enter a value for encryption"));

            return Command::INVALID;
        }

        $encode = $this->aESService->encode([
            'value' => trim($value),
        ]);

        $output->writeln($this->successOutput("\t>> {$encode['value']}"));

        return Command::SUCCESS;
    }
}
