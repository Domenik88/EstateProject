<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 22.09.2020
 *
 * @package estateblock20
 */

namespace App\Service\Geo;

use App\Service\Geo\Provider\GeoCoderProvider;
use Geocoder\Query\GeocodeQuery;
use Geocoder\Query\ReverseQuery;

class GeoCodeService
{
    private GeoCoderProvider $geoCoderProvider;

    public function __construct(GeoCoderProvider $geoCoderProvider)
    {
        $this->geoCoderProvider = $geoCoderProvider;
    }

    public function getLatLong(string $address)
    {
        $result = $this->geoCoderProvider->getGeoCoder()->geocodeQuery(GeocodeQuery::create($address));
        if ( !$result->isEmpty() ) {
            $coordinates = $result->first()->getCoordinates();
            return [ 'lat' => $coordinates->getLatitude(), 'lng' => $coordinates->getLongitude() ];
        }
        return null;
    }

    public function getAddress(string $lat, string $lng)
    {
        $result = $this->geoCoderProvider->getGeoCoder()->reverseQuery(ReverseQuery::fromCoordinates($lat, $lng));
        if ( !$result->isEmpty() ) {
            $i = $index = 0;
            foreach ( $result->all() as $arItem ) {
                if ( $arItem->getStreetName() != null && $arItem->getLocality() != null && $arItem->getPostalCode() != null && $arItem->getAdditionalData()['StateName'] != null ) {
                    $index = $i;
                    break;
                }
                $i++;
            }
            return [ 'street' => $result->get($index)->getStreetName(), 'city' => $result->get($index)->getLocality(), 'state' => $result->get($index)->getAdditionalData()['StateName'], 'zipcode' => $result->get($index)->getPostalCode() ];
        }
        return null;
    }

}