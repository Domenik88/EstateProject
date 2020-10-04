<?php

namespace App\Command;

use App\Repository\ListingRepository;
use App\Service\Feed\DdfListingMasterService;
use App\Service\Feed\DdfService;
use App\Service\Feed\FeedService;
use App\Service\Listing\ListingService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FetchListingUpdatesCommand extends Command
{
    protected static $defaultName = 'app:listing-updates';

    private DdfService $ddfService;
    private LoggerInterface $logger;
    private ListingRepository $listingRepository;
    private ListingService $listingService;
    private FeedService $feedService;
    private DdfListingMasterService $ddfListingMasterService;

    public function __construct(DdfService $ddfService, LoggerInterface $logger, ListingRepository $listingRepository, ListingService $listingService, FeedService $feedService, DdfListingMasterService $ddfListingMasterService)
    {
        $this->ddfService = $ddfService;
        $this->logger = $logger;
        $this->listingRepository = $listingRepository;
        $this->listingService = $listingService;
        $this->feedService = $feedService;
        $this->ddfListingMasterService = $ddfListingMasterService;
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
        $this->ddfListingMasterService->upsertDdfMasterList();
        die;
        $commandLastRunTimeDate = new \DateTime();
        $io = new SymfonyStyle($input, $output);

        if ($this->feedService->isFeedBusy('ddf')) {
            $io->warning('Feed is busy');
            return Command::SUCCESS;
        }
        $lastRunTimeDate = $this->feedService->setBusyByFeedName('ddf',true);

        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            $io->note(sprintf('You passed an option: %s', $input->getOption('option1')));
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        $searchOffset = null;
        $searchCount = 0;
        $totalSearchResult = 0;
        try {
            do {
                $searchResult = $this->ddfService->searchUpdatedListings($lastRunTimeDate, $searchOffset);
                foreach ( $searchResult->results as $result ) {
                    unset($result['AnalyticsClick'],$result['AnalyticsView']);
                    $this->listingService->upsertFromDdfResult($result);
                    $searchCount++;
                }
                $searchOffset = $searchResult->nextRecordOffset;
                $totalSearchResult = $searchResult->totalCount;
            } while ( $searchResult->moreAvailable );
            $this->feedService->setLastRunTimeByFeedName('ddf', $commandLastRunTimeDate);
            $io->success($searchCount);
            $io->success($totalSearchResult);
        } catch (\Exception $e) {
            $io->note($e);
            $this->logger->error($e->getMessage());
            $this->logger->error($e->getTraceAsString());
        } finally {
            $this->feedService->setBusyByFeedName('ddf',false);
        }

        return Command::SUCCESS;
    }
}
