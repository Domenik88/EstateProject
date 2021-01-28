<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 28.01.2021
 *
 * @package estateblock20
 */

namespace App\Service\Listing;

use App\Repository\ListingRepository;

class ListingSearchService
{
    private ListingRepository $listingRepository;

    public function __construct(ListingRepository $listingRepository)
    {
        $this->listingRepository = $listingRepository;
    }

    public function searchListings($criteria = []): ?array
    {
        $result = $this->listingRepository->findBy($criteria);
        dump($result);
        die;
        return [];
    }
}