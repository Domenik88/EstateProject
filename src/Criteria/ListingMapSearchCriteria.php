<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 29.01.2021
 *
 * @package estateblock20
 */

namespace App\Criteria;

class ListingMapSearchCriteria
{
    public ?string $city;
    public ?string $stateOrProvince;

    public function __construct(?string $city,
                                ?string $stateOrProvince)
    {
        $this->city = $city;
        $this->stateOrProvince = $stateOrProvince;
    }

    private function keyWordsToArray(?string $keyWords): ?array
    {
        if ( is_null($keyWords) ) {
            return null;
        }
        return explode(',', str_replace(' ', '', $keyWords));
    }

}