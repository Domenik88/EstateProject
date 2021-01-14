<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 13.01.2021
 *
 * @package estateblock20
 */

namespace App\Service\Geo;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class PolygonType extends Type
{
    const POLYGON = 'polygon';

    public function getName()
    {
        return self::POLYGON;
    }

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'POLYGON';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $polygonPoints = explode(',', trim($value,'()'));
        $points = [];
        foreach ( $polygonPoints as $point ) {
            [$latitude, $longitude] = sscanf($value, '(%f,%f)');
            $points[] = new Point($latitude, $longitude);
        }

        return $points;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value instanceof Polygon) {
            $polygonPoints = [];
            foreach ( $value->getPoints() as $point ) {
                $polygonPoints[] = sprintf('(%F,%F)', $point->getLatitude(), $point->getLongitude());
            }
            return '(' . implode(',', $polygonPoints) . ')';
        }

        return $value;
    }
}