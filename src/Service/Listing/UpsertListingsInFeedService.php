<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 04.10.2020
 *
 * @package estateblock20
 */

namespace App\Service\Listing;


use App\Repository\ListingRepository;

class UpsertListingsInFeedService
{
    private ListingRepository $listingRepository;
    private ListingService $listingService;

    public function __construct(ListingRepository $listingRepository, ListingService $listingService)
    {
        $this->listingRepository = $listingRepository;
        $this->listingService = $listingService;
    }

    public function upsertListings()
    {
        $this->listingRepository->updateDdfDeletedListings();
        $result = $this->listingRepository->getMissingListingsFromDdfListingMaster();
        dump($result[1]);
        $this->upsertMissingListingsFromListingMaster($result);
    }

    public function upsertMissingListingsFromListingMaster(array $result)
    {
//        $this->listingService->upsertMissingListingsFromListingMaster();
    }

}