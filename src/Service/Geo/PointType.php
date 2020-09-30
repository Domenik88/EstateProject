<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 29.09.2020
 *
 * @package estateblock20
 */

namespace App\Service\Geo;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class PointType extends Type
{
    const POINT = 'point';

    public function getName()
    {
        return self::POINT;
    }

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'POINT';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        list($latitude, $longitude) = sscanf($value, '(%f,%f)');

        return new Point($latitude, $longitude);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value instanceof Point) {
            $value = sprintf('(%F,%F)', $value->getLatitude(), $value->getLongitude());
        }

        return $value;
    }

//    public function canRequireSQLConversion()
//    {
//        return true;
//    }
//
//    public function convertToPHPValueSQL($sqlExpr, $platform)
//    {
//        return sprintf('AsText(%s)', $sqlExpr);
//    }
//
//    public function convertToDatabaseValueSQL($sqlExpr, AbstractPlatform $platform)
//    {
//        return sprintf('PointFromText(%s)', $sqlExpr);
//    }
}