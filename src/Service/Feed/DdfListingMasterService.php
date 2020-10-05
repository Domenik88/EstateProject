<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 03.10.2020
 *
 * @package estateblock20
 */

namespace App\Service\Feed;


use App\Repository\ListingMasterRepository;
use App\Repository\ListingRepository;

class DdfListingMasterService
{
    private DdfService $ddfService;
    private ListingMasterRepository $listingMasterRepository;
    private ListingRepository $listingRepository;

    public function __construct(DdfService $ddfService, ListingMasterRepository $listingMasterRepository, ListingRepository $listingRepository)
    {
        $this->ddfService = $ddfService;
        $this->listingMasterRepository = $listingMasterRepository;
        $this->listingRepository = $listingRepository;
    }

    public function syncListingRecords()
    {
        $this->listingMasterRepository->truncateListingMasterTable();
        $masterList = $this->ddfService->getMasterList();
        $this->listingMasterRepository->insertMasterList($masterList);
        $this->listingRepository->deleteListings('ddf');
        $this->listingRepository->createMissingListingsFromDdfListingMaster();
        $this->listingMasterRepository->truncateListingMasterTable();
    }

}