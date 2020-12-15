<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 26.11.2020
 *
 * @package estateblock20
 */

namespace App\Service\Listing;

use App\Repository\ListingRepository;
use App\Service\Geo\Point;

class ListingSimilarSearch
{
    private ListingRepository $listingRepository;
    private ListingSearchDataService $listingSearchDataService;

    public function __construct(ListingRepository $listingRepository, ListingSearchDataService $listingSearchDataService)
    {
        $this->listingRepository = $listingRepository;
        $this->listingSearchDataService = $listingSearchDataService;
    }

    public function getSimilarListingsData($listingData): ?object
    {
        $yearBuiltRange = $this->inRange($listingData->metrics->yearBuilt,ListingConstants::YEAR_BUILT);
        $livingAreaRange = $this->inRange($listingData->metrics->sqrtFootage,ListingConstants::LIVING_AREA);
        $lotSizeRange = $this->inRange($listingData->metrics->lotSize,ListingConstants::LOT_SIZE);

        $similarListingsData = $this->listingRepository->getSimilarListings($listingData->type, $listingData->ownershipType, $listingData->metrics->bedRooms, $livingAreaRange, $lotSizeRange, $yearBuiltRange, $listingData->coordinates, $listingData->mlsNumber);
        $similarListings = [];
        foreach ( $similarListingsData as $similarListingData ) {
            $similarListings[] = $this->listingSearchDataService->constructSearchListingData($similarListingData);
        }
        return (object)$similarListings;
    }

    function inRange($number, $range)
    {
        foreach ( $range as $item ) {
            if ( $number >= $item[0] && $number <= $item[1] ) {
                return $item;
            }
        }
        return null;
    }
}