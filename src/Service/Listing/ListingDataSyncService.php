<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 06.10.2020
 *
 * @package estateblock20
 */

namespace App\Service\Listing;


use App\Entity\Listing;
use App\Service\Feed\DdfService;

class ListingDataSyncService
{
    private ListingService $listingService;
    private DdfService $ddfService;

    public function __construct(ListingService $listingService, DdfService $ddfService)
    {
        $this->listingService = $listingService;
        $this->ddfService = $ddfService;
    }

    public function syncAllListingData(Listing $listing)
    {
        $listingForProcessingData = $this->ddfService->getListingByFeedListingId($listing->getFeedListingID());
        unset($listingForProcessingData['AnalyticsClick'],$listingForProcessingData['AnalyticsView']);
        $this->listingService->upsertFromDdfResult($listingForProcessingData,false);
    }
}