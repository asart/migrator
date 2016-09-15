<?php
namespace Migrator\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Migrator\MigrationWriterInterface;

/**
 * Class Status
 * @package Migrator\Command
 */
class CreateCommand extends BaseCommand
{
    /**
     * @var MigrationWriterInterface
     */
    private $writer;

    /**
     * @param MigrationWriterInterface $writer
     */
    public function setWriter($writer)
    {
        $this->writer = $writer;
    }

    protected function configure()
    {
        $this
            ->setName('create')
            ->setDescription('Create new migration file')
            ->addArgument(
                'database',
                InputArgument::OPTIONAL,
                'Database name',
                'default'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $database = $input->getArgument('database');

        if ($this->writer->createNextVersionUp($database)) {
            $output->writeln('success created migration ' . $this->writer->getNewMigration());
        } else {
            $output->writeln('create migration filed');
        }
    }
}
