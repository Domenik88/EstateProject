<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 21.12.2020
 *
 * @package estateblock20
 */

namespace App\Service\Listing;

class CitiesCounterResult
{
    public string $city;
    public int $count;
    public string $feedID;
    public string $stateOrProvince;

    public function __construct(string $city, int $count, string $feedID, string $stateOrProvince)
    {
        $this->city = $city;
        $this->count = $count;
        $this->feedID = $feedID;
        $this->stateOrProvince = $stateOrProvince;
    }
}