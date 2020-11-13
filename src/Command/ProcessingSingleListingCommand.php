<?php

namespace App\Command;

use App\Repository\ListingMasterRepository;
use App\Repository\ListingRepository;
use App\Service\Feed\DdfService;
use App\Service\Listing\ListingConstants;
use App\Service\Listing\ListingDataSyncService;
use App\Service\Listing\ListingGeoService;
use App\Service\Listing\ListingMediaSyncService;
use App\Service\Listing\ListingService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
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
    private ListingGeoService $listingGeoService;
    private ListingMediaSyncService $listingMediaSyncService;
    private ListingMasterRepository $listingMasterRepository;
    private ListingDataSyncService $listingDataSyncService;
    private DdfService $ddfService;
    private EntityManagerInterface $entityManager;

    public function __construct(ListingMasterRepository $listingMasterRepository, LoggerInterface $logger, ListingRepository $listingRepository, ListingService $listingService, ListingGeoService $listingGeoService, ListingMediaSyncService $listingMediaSyncService, ListingDataSyncService $listingDataSyncService, DdfService $ddfService, EntityManagerInterface $entityManager)
    {
        $this->logger = $logger;
        $this->listingRepository = $listingRepository;
        $this->listingService = $listingService;
        $this->listingGeoService = $listingGeoService;
        $this->listingMediaSyncService = $listingMediaSyncService;
        $this->listingMasterRepository = $listingMasterRepository;
        $this->listingDataSyncService = $listingDataSyncService;
        $this->ddfService = $ddfService;
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    const BATCH_SIZE = 100;

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('bsize', InputArgument::OPTIONAL, 'Batch size, default 100')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        if ($input->getArgument('bsize')) {
            $batchSize = (int)$input->getArgument('bsize');
        } else {
            $batchSize = self::BATCH_SIZE;
        }
        $this->ddfService->connect();
        $batchListings = $this->listingService->getBatchListingsForProcessing('ddf',$batchSize);
        $this->listingService->setBatchProcessingStatus($batchListings, ListingConstants::PROCESSING_PROCESSING_LISTING_STATUS);
        foreach ($batchListings as $singleListing) {
            try {
                $io->success("Processing listing MLS_NUM: {$singleListing->getMlsNum()} Listing Feed ID: {$singleListing->getFeedListingID()}");
                $singleListingWithData = $this->listingDataSyncService->syncAllListingData($singleListing);
                $singleListingWithPhotos = $this->listingMediaSyncService->syncAllListingPhotos($singleListingWithData);
                $this->listingGeoService->syncListingCoordinatesFromAddress($singleListingWithPhotos);

                // Command body

                $this->listingService->setListingProcessingStatus($singleListing, ListingConstants::NONE_PROCESSING_LISTING_STATUS);
                $this->listingService->setListingStatus($singleListing, ListingConstants::LIVE_LISTING_STATUS);
            } catch (\Exception $e) {
                $this->listingService->setListingProcessingStatus($singleListing, ListingConstants::ERROR_PROCESSING_LISTING_STATUS);
                $this->logger->error($e->getMessage());
                $this->logger->error($e->getTraceAsString());
            } finally {
                $this->entityManager->clear();
            }
        }
        return Command::SUCCESS;
    }
}
