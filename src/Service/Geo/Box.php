<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 14.10.2020
 *
 * @package estateblock20
 */

namespace App\Service\Geo;


class Box
{
    /**
     * @param float $northEast
     * @param float $southWest
     */

    public function __construct(float $northEast, float $southWest)
    {
        $this->northEast = $northEast;
        $this->southWest = $southWest;
    }

    /**
     * @return float
     */

    public function getNorthEast()
    {
        return $this->northEast;
    }

    /**
     * @return float
     */

    public function getSouthWest()
    {
        return $this->southWest;
    }
}