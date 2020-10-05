<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 04.10.2020
 *
 * @package estateblock20
 */

namespace App\Service\Listing;


use App\Repository\ListingMasterRepository;
use App\Repository\ListingRepository;

class UpsertListingsInFeedService
{
    private ListingRepository $listingRepository;
    private ListingService $listingService;
    private ListingMasterRepository $listingMasterRepository;

    public function __construct(ListingRepository $listingRepository, ListingService $listingService, ListingMasterRepository $listingMasterRepository)
    {
        $this->listingRepository = $listingRepository;
        $this->listingService = $listingService;
        $this->listingMasterRepository = $listingMasterRepository;
    }

    public function syncListingRecords()
    {
        $this->listingRepository->deleteListings('ddf');
        $this->listingRepository->getMissingListingsFromDdfListingMaster();
        $this->listingMasterRepository->truncateListingMasterTable();
    }

}