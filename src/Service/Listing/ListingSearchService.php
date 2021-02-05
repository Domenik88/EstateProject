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
use App\Entity\Page;
use App\Repository\ListingRepository;

class ListingSearchService
{
    private ListingRepository $listingRepository;
    private ListingSearchDataService $listingSearchDataService;
    private ListingService $listingService;

    public function __construct(ListingRepository $listingRepository,
                                ListingSearchDataService $listingSearchDataService, ListingService $listingService)
    {
        $this->listingRepository = $listingRepository;
        $this->listingSearchDataService = $listingSearchDataService;
        $this->listingService = $listingService;
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

    public function getListingsForSearchPage(Page $page): ?array
    {
        $content = explode(',',$page->getContent());
        $criteria = new ListingMapSearchCriteria($content[0], $this->listingService->getProvinceName($content[1]));
        return $this->searchListings($criteria);
    }

}