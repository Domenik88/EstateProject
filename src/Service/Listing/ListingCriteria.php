<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 22.12.2020
 *
 * @package estateblock20
 */

namespace App\Service\Listing;

class ListingCriteria
{
    public string $feedId;
    public array $statuses;

    public function __construct(string $feedId, array $statuses)
    {
        $this->feedId = $feedId;
        $this->statuses = $statuses;
    }
}