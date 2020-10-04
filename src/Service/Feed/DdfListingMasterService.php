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
    const LIMIT = 100;

    public function __construct(DdfService $ddfService, ListingMasterRepository $listingMasterRepository)
    {
        $this->ddfService = $ddfService;
        $this->listingMasterRepository = $listingMasterRepository;
    }

    public function upsertDdfMasterList()
    {
        $offset = 1;
        $page = 0;
        do {
            $masterList = $this->ddfService->getMasterList(self::LIMIT,$offset);
            $this->listingMasterRepository->insertMasterList($masterList['currentPage']);
            dump($masterList);
            die;
            $offset = $offset + self::LIMIT;
            $page++;
        } while ($page < $masterList['totalPages']);
    }
}