<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 13.01.2021
 *
 * @package estateblock20
 */

namespace App\Service\Geo;

class Polygon
{
    private array $points;

    public function __construct(array $points)
    {
        $this->points = $points;
    }

    /**
     * @return Point[]
     */
    public function getPoints(): array
    {
        return $this->points;
    }

    /**
     * @param Point[] $points
     */
    public function setPoints(array $points)
    {
        $this->points = $points;
    }
}