<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 29.01.2021
 *
 * @package estateblock20
 */

namespace App\Criteria;

class ListingSearchMapCriteria
{
    public ?string $city;
//    public ?int $beds;
//    public ?int $baths;
//    public ?array $livingArea;
//    public ?int $lotSize;
//    public ?array $yearBuilt;
//    public ?array $type;
//    public ?array $price;
//    public ?array $keyWords;

    public function __construct(?string $city
//        ?int $beds = null,
//                                ?int $baths = null,
//                                ?array $livingArea = null,
//                                ?int $lotSize = null,
//                                ?array $yearBuilt = null,
//                                ?array $type = null,
//                                ?array $price = null,
//                                ?string $keyWords = null
    )
    {
        $this->city = $city;
//        $this->beds = $beds;
//        $this->baths = $baths;
//        $this->livingArea = $livingArea;
//        $this->lotSize = $lotSize;
//        $this->yearBuilt = $yearBuilt;
//        $this->type = $type;
//        $this->price = $price;
//        $this->keyWords = $this->keyWordsToArray($keyWords);
    }

    private function keyWordsToArray(?string $keyWords): ?array
    {
        if ( is_null($keyWords) ) {
            return null;
        }

        return explode(',', str_replace(' ','',$keyWords));
    }

}