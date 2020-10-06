<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 04.10.2020
 *
 * @package estateblock20
 */

namespace App\Service\Feed;


use App\Service\Listing\ListingService;

class SearchUpdatedDdfListingsService
{
    private DdfService $ddfService;
    private ListingService $listingService;

    public function __construct(DdfService $ddfService, ListingService $listingService)
    {
        $this->ddfService = $ddfService;
        $this->listingService = $listingService;
    }

    public function searchAndRecordUpdatedListings(\DateTimeInterface $lastRunTimeDate)
    {
        $searchOffset = null;
        do {
            $searchResult = $this->ddfService->searchUpdatedListings($lastRunTimeDate,$searchOffset);
            foreach ( $searchResult->results as $result ) {
                unset($result['AnalyticsClick'],$result['AnalyticsView']);
                $this->listingService->upsertFromDdfResult($result);
            }
            $searchOffset = $searchResult->nextRecordOffset;
        } while ( $searchResult->moreAvailable );
    }
}