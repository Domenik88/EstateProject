<?php

namespace App\Command;

use App\Service\Feed\DdfService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UserCommand extends Command
{
    protected static $defaultName = 'app:user-command';

    private DdfService $ddfService;
    private LoggerInterface $logger;

    public function __construct(DdfService $ddfService, LoggerInterface $logger)
    {
        $this->ddfService = $ddfService;
        $this->logger = $logger;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            $io->note(sprintf('You passed an option: %s', $input->getOption('option1')));
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        $date = new \DateTime();
        $date = $date->modify('-8 hour');
        $searchOffset = null;
        $searchCount = 0;
        $totalSearchResult = 0;
        try {
            do {
                $searchResult = $this->ddfService->searchUpdatedListings($date, $searchOffset);
                foreach ( $searchResult->results as $result ) {
                    $io->note($result['UnparsedAddress']);
                    $searchCount++;
                }
                $searchOffset = $searchResult->nextRecordOffset;
                $totalSearchResult = $searchResult->totalCount;
            } while ( $searchResult->moreAvailable );
        } catch (\Exception $e) {
            $io->note($e);
            $this->logger->error($e->getMessage());
            $this->logger->error($e->getTraceAsString());
        }
        $io->success($searchCount);
        $io->success($totalSearchResult);

        return Command::SUCCESS;
    }
}
