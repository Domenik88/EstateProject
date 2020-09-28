<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 22.09.2020
 *
 * @package estateblock20
 */

namespace App\Service\Listing;


class ListingListSearchResult
{
    public int $listingCount;
    public array $results;
    public int $currentPage;
    public int $pagesCount;

    public function __construct(int $listingCount, array $results, int $currentPage, int $pagesCount)
    {
        $this->listingCount = $listingCount;
        $this->results = $results;
        $this->currentPage = $currentPage;
        $this->pagesCount = $pagesCount;
    }
}