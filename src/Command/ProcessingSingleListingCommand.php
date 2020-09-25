<?php

namespace App\Command;

use App\Entity\Listing;
use App\Repository\ListingRepository;
use App\Service\ListingService;
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
    private Listing $singleListing;

    public function __construct(LoggerInterface $logger, ListingRepository $listingRepository, ListingService $listingService)
    {
        $this->logger = $logger;
        $this->listingRepository = $listingRepository;
        $this->listingService = $listingService;
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
        try {
            $this->singleListing = $this->listingService->getSingleListingForProcessing('ddf');
            $this->listingService->setListingProcessingStatus($this->singleListing, 'processing');
            $io = new SymfonyStyle($input, $output);
            $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

            // Command body

            $this->listingService->setListingProcessingStatus($this->singleListing, 'none');
            $this->listingService->setListingStatus($this->singleListing, 'live');
        } catch (\Exception $e) {
            $this->listingService->setListingProcessingStatus($this->singleListing, 'error');
            $this->logger->error($e->getMessage());
            $this->logger->error($e->getTraceAsString());
        }
        return Command::SUCCESS;
    }
}
