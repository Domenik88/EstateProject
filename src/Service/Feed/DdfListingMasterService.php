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

class DdfListingMasterService
{
    private DdfService $ddfService;
    private ListingMasterRepository $listingMasterRepository;

    public function __construct(DdfService $ddfService, ListingMasterRepository $listingMasterRepository)
    {
        $this->ddfService = $ddfService;
        $this->listingMasterRepository = $listingMasterRepository;
    }

    public function upsertDdfMasterList()
    {
            $masterList = $this->ddfService->getMasterList();
            $this->listingMasterRepository->insertMasterList($masterList);
    }
}