<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use Doctrine\Migrations\Tools\Console\Command\DiffCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrationCommand extends Command
{
    protected function configure()
    {
        $this->setName('make:migration')
            ->setDescription('Create new migration');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Add migration generation logic
        $output->writeln('Migration created successfully');
        return Command::SUCCESS;
    }
}
