<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 29.01.2021
 *
 * @package estateblock20
 */

namespace App\Criteria;

class ListingSearchCriteria
{
    public ?string $mlsNum;
    public ?int $beds;
    public ?int $baths;

    public function __construct(string $mlsNum = null, ?int $beds = null, ?int $baths = null)
    {
        $this->mlsNum = $mlsNum;
        $this->beds = $beds;
        $this->baths = $baths;
    }

    public function toArray(): ?array
    {
        $return = [];
        if ($this->mlsNum){
            $return['mlsNum'] = $this->getMlsNum();
        }
        if ($this->beds){
            $return['bedrooms'] = $this->getBeds();
        }
        if ($this->beds){
            $return['bathrooms'] = $this->getBaths();
        }
        return $return;
    }

    private function getMlsNum(): ?string
    {
        return $this->mlsNum;
    }

    private function getBeds(): ?int
    {
        return $this->beds;
    }

    private function getBaths(): ?int
    {
        return $this->baths;
    }

}