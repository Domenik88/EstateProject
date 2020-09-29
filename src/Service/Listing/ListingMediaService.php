<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 29.09.2020
 *
 * @package estateblock20
 */

namespace App\Service\Listing;


use App\Entity\Listing;
use App\Service\AwsService;
use App\Service\Feed\DdfService;
use Symfony\Component\Filesystem\Filesystem;

class ListingMediaService
{
    private DdfService $ddfService;
    private AwsService $awsService;
    private ListingService $listingService;
    private Filesystem $filesystem;

    public function __construct(DdfService $ddfService, AwsService $awsService, ListingService $listingService, Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
        $this->listingService = $listingService;
        $this->awsService = $awsService;
        $this->ddfService = $ddfService;
    }

    public function syncAllListingPhotos(Listing $listing)
    {
        $photoNamesArray = $this->ddfService->getListingPhotosFromFeed($listing->getFeedListingID(),$listing->getFeedID());
        $listingPicPathForUpload = $listing->getFeedID() . '/' . $listing->getFeedListingID();
        $this->awsService->upload($listingPicPathForUpload);
        $this->listingService->setListingPhotosNamesObject($listing,$photoNamesArray);
        $this->filesystem->remove(sys_get_temp_dir() . ListingConstants::UPLOAD_LISTING_PIC_PATH);
    }
}