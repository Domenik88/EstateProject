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
use Symfony\Component\Filesystem\Filesystem;

class ListingMediaSyncService
{
    private DdfService $ddfService;
    private AwsService $awsService;
    private ListingService $listingService;
    private Filesystem $filesystem;

    public function __construct(DdfService $ddfService, AwsService $awsService, ListingService $listingService, Filesystem $filesystem)
    {
        $this->listingService = $listingService;
        $this->awsService = $awsService;
        $this->ddfService = $ddfService;
        $this->filesystem = $filesystem;
    }

    public function syncAllListingPhotos(Listing $listing)
    {
        $listingPicPathForUpload = sys_get_temp_dir() . ListingConstants::UPLOAD_LISTING_PIC_PATH . 'listing/' . $listing->getFeedID() . '/' . $listing->getFeedListingID();
        $cloudDestination = 'listing/' . $listing->getFeedID() . '/' . $listing->getFeedListingID();
        $photoNamesArray = $this->ddfService->fetchListingPhotosFromFeed($listing->getFeedListingID(),$listingPicPathForUpload);
        $this->awsService->upload($listingPicPathForUpload,$cloudDestination);
        $this->listingService->setListingPhotosNamesObject($listing,$photoNamesArray);
        $this->filesystem->remove($listingPicPathForUpload);
    }

}