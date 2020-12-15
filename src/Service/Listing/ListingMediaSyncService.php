<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 01.10.2020
 *
 * @package estateblock20
 */

namespace App\Service\Listing;


use App\Entity\Listing;
use App\Service\AwsService;
use App\Service\Feed\DdfService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;

class ListingMediaSyncService
{
    private DdfService $ddfService;
    private AwsService $awsService;
    private ListingService $listingService;
    private Filesystem $filesystem;
    private LoggerInterface $logger;

    public function __construct(DdfService $ddfService, AwsService $awsService, ListingService $listingService, Filesystem $filesystem, LoggerInterface $logger)
    {
        $this->listingService = $listingService;
        $this->awsService = $awsService;
        $this->ddfService = $ddfService;
        $this->filesystem = $filesystem;
        $this->logger = $logger;
    }

    public function syncAllListingPhotos(Listing $listing): Listing
    {
        $listingPicPathForUpload = sys_get_temp_dir() . ListingConstants::UPLOAD_LISTING_PIC_PATH . 'listing/' . $listing->getFeedID() . '/' . $listing->getFeedListingID() . '/';
        $cloudDestination = 'listings/' . $listing->getFeedID() . '/' . $listing->getFeedListingID() . '/';
        try {
            if ( !is_dir($listingPicPathForUpload) ) {
                $this->filesystem->mkdir($listingPicPathForUpload);
            }
            $photoNamesArray = $this->ddfService->fetchListingPhotosFromFeed($listing, $listingPicPathForUpload);
            $this->awsService->upload($listingPicPathForUpload, $cloudDestination);
            $singleListingWithPhotos = $this->listingService->setListingPhotosNamesObject($listing, $photoNamesArray);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $this->logger->error($e->getTraceAsString());
            throw $e;
        } finally {
            $this->filesystem->remove($listingPicPathForUpload);
        }

        return $singleListingWithPhotos;
    }

}