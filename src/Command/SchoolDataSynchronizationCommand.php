<?php

namespace App\Command;

use App\Service\Geo\Point;
use App\Service\Geo\Polygon;
use App\Service\School\SchoolService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SchoolDataSynchronizationCommand extends Command
{
    protected static $defaultName = 'app:school-data-synchronization';
    private SchoolService $schoolService;
    private string $projectDir;

    public function __construct(SchoolService $schoolService, string $projectDir)
    {
        $this->schoolService = $schoolService;
        $this->projectDir = $projectDir;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Inserting school data from file to database')
            ->addArgument('filePath', InputArgument::OPTIONAL, 'School data file path')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $filePath = $input->getArgument('filePath');
        if (substr($filePath,0,1) != '/')
        {
            $filePath = $this->projectDir . '/' . str_ireplace('../', '', $filePath);
        }
        $this->schoolService->index($filePath);

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
