<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 28.01.2021
 *
 * @package estateblock20
 */

namespace App\Service\Listing;

use App\Criteria\ListingMapSearchCriteria;
use App\Repository\ListingRepository;

class ListingSearchService
{
    private ListingRepository $listingRepository;
    private ListingSearchDataService $listingSearchDataService;

    public function __construct(ListingRepository $listingRepository,
                                ListingSearchDataService $listingSearchDataService)
    {
        $this->listingRepository = $listingRepository;
        $this->listingSearchDataService = $listingSearchDataService;
    }

    public function searchListings(ListingMapSearchCriteria $criteria): ?array
    {
        $result = [];
        $searchResult = $this->listingRepository->searchListingsByMapCriteria($criteria);
        foreach ( $searchResult as $item ) {
            $result[] = $this->listingSearchDataService->constructSearchListingData($item);
        }
        return $result;
    }
}