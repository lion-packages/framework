<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Lion\Command\Command;
use Lion\Security\Validation;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ServerHashCommand extends Command
{
    private Validation $validation;

    /**
     * @required
     * */
    public function setValidation(Validation $validation): ServerHashCommand
    {
        $this->validation = $validation;

        return $this;
    }

	protected function configure(): void
	{
		$this
            ->setName('hash')
            ->setDescription('Command to generate a secure hash');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$output->writeln($this->successOutput("\t>>  HASH: {$this->validation->sha256(uniqid())}"));

		return Command::SUCCESS;
	}
}
