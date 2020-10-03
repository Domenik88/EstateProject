<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 03.10.2020
 *
 * @package estateblock20
 */

namespace App\Service\Listing;



class ListingDataRawService
{
    private ListingService $listingService;

    public function __construct(ListingService $listingService)
    {
        $this->listingService = $listingService;
    }

    public function rawData(array $listing)
    {
        unset($listing['AnalyticsClick'],$listing['AnalyticsView']);
        $rawData = json_decode(json_encode($listing,JSON_FORCE_OBJECT));
        $this->listingService->upsertRawData($listing,$rawData);
    }
}