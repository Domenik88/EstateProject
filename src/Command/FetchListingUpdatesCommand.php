<?php

namespace App\Command;

use App\Criteria\ListingMapSearchCriteria;
use App\Repository\ListingRepository;
use App\Service\Feed\DdfListingMasterService;
use App\Service\Feed\DdfService;
use App\Service\Feed\FeedService;
use App\Service\Feed\SearchUpdatedDdfListingsService;
use App\Service\Listing\ListingSearchService;
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
    private SearchUpdatedDdfListingsService $searchUpdatedDdfListingsService;

    public function __construct(DdfService $ddfService, LoggerInterface $logger, ListingRepository $listingRepository, ListingService $listingService, FeedService $feedService, DdfListingMasterService $ddfListingMasterService, SearchUpdatedDdfListingsService $searchUpdatedDdfListingsService)
    {
        $this->ddfService = $ddfService;
        $this->logger = $logger;
        $this->listingRepository = $listingRepository;
        $this->listingService = $listingService;
        $this->feedService = $feedService;
        $this->ddfListingMasterService = $ddfListingMasterService;
        $this->searchUpdatedDdfListingsService = $searchUpdatedDdfListingsService;
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
        $commandLastRunTimeDate = new \DateTime();
        $io = new SymfonyStyle($input, $output);
        $io->success("Start fetching listings");
        if ($this->feedService->isFeedBusy('ddf')) {
            $io->warning('Feed is busy');
            return Command::SUCCESS;
        }
        $lastRunTimeDate = $this->feedService->setBusyByFeedName('ddf',true);
        try {
            $this->searchUpdatedDdfListingsService->searchAndRecordUpdatedListings($lastRunTimeDate);
            $this->ddfListingMasterService->syncListingRecords();

            $this->feedService->setLastRunTimeByFeedName('ddf', $commandLastRunTimeDate);
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
