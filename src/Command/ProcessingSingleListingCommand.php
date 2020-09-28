<?php

namespace App\Command;

use App\Entity\Listing;
use App\Repository\ListingRepository;
use App\Service\AwsService;
use App\Service\Feed\DdfService;
use App\Service\Listing\ListingInterface;
use App\Service\Listing\ListingService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

class ProcessingSingleListingCommand extends Command
{
    protected static $defaultName = 'app:processing-single-listing';
    private LoggerInterface $logger;
    private ListingRepository $listingRepository;
    private ListingService $listingService;
    private Listing $singleListing;
    private DdfService $ddfService;
    private AwsService $awsService;
    private Filesystem $filesystem;

    public function __construct(LoggerInterface $logger, ListingRepository $listingRepository, ListingService $listingService, DdfService $ddfService, AwsService $awsService, Filesystem $filesystem)
    {
        $this->logger = $logger;
        $this->listingRepository = $listingRepository;
        $this->listingService = $listingService;
        $this->ddfService = $ddfService;
        $this->awsService = $awsService;
        $this->filesystem = $filesystem;
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
        $this->singleListing = $this->listingService->getSingleListingForProcessing('ddf');
        try {
            $this->listingService->setListingProcessingStatus($this->singleListing, ListingInterface::PROCESSING_PROCESSING_LISTING_STATUS);
            $io = new SymfonyStyle($input, $output);
            $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

            $photoNamesArray = $this->ddfService->getListingPhotosFromFeed($this->singleListing->getFeedListingID(),$this->singleListing->getFeedID());
            $listingPicPathForUpload = $this->singleListing->getFeedID() . '/' . $this->singleListing->getFeedListingID();
            $this->awsService->upload($listingPicPathForUpload);
            $this->listingService->setListingPhotosNamesObject($this->singleListing,$photoNamesArray);
            $this->filesystem->remove(sys_get_temp_dir() . ListingInterface::UPLOAD_LISTING_PIC_PATH);

            // Command body

            $this->listingService->setListingProcessingStatus($this->singleListing, ListingInterface::NONE_PROCESSING_LISTING_STATUS);
            $this->listingService->setListingStatus($this->singleListing, ListingInterface::LIVE_LISTING_STATUS);
        } catch (\Exception $e) {
            $this->listingService->setListingProcessingStatus($this->singleListing, ListingInterface::ERROR_PROCESSING_LISTING_STATUS);
            $this->logger->error($e->getMessage());
            $this->logger->error($e->getTraceAsString());
        }
        return Command::SUCCESS;
    }
}
