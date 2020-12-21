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

    public function __construct(string $city, int $count)
    {
        $this->city = $city;
        $this->count = $count;
    }
}