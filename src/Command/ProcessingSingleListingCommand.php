<?php

namespace App\Command;

use App\Repository\ListingRepository;
use App\Service\Listing\ListingConstants;
use App\Service\Listing\ListingGeoService;
use App\Service\Listing\ListingMediaService;
use App\Service\Listing\ListingService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ProcessingSingleListingCommand extends Command
{
    protected static $defaultName = 'app:processing-single-listing';
    private LoggerInterface $logger;
    private ListingRepository $listingRepository;
    private ListingService $listingService;
    private ListingMediaService $listingMediaService;
    private ListingGeoService $listingGeoService;

    public function __construct(LoggerInterface $logger, ListingRepository $listingRepository, ListingService $listingService, ListingMediaService $listingMediaService, ListingGeoService $listingGeoService)
    {
        $this->logger = $logger;
        $this->listingRepository = $listingRepository;
        $this->listingService = $listingService;
        $this->listingMediaService = $listingMediaService;
        $this->listingGeoService = $listingGeoService;
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
        $singleListing = $this->listingService->getSingleListingForProcessing('ddf');
        try {
            $this->listingService->setListingProcessingStatus($singleListing, ListingConstants::PROCESSING_PROCESSING_LISTING_STATUS);
            $io = new SymfonyStyle($input, $output);
            $io->success("Processed listing {$singleListing->getMlsNum()}");
            $this->listingMediaService->syncAllListingPhotos($singleListing);
            $this->listingGeoService->syncListingCoordinatesFromAddress($singleListing);

            // Command body

            $this->listingService->setListingProcessingStatus($singleListing, ListingConstants::NONE_PROCESSING_LISTING_STATUS);
            $this->listingService->setListingStatus($singleListing, ListingConstants::LIVE_LISTING_STATUS);
        } catch (\Exception $e) {
            $this->listingService->setListingProcessingStatus($singleListing, ListingConstants::ERROR_PROCESSING_LISTING_STATUS);
            $this->logger->error($e->getMessage());
            $this->logger->error($e->getTraceAsString());
        }
        return Command::SUCCESS;
    }
}
