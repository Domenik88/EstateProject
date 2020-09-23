<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 22.09.2020
 *
 * @package estateblock20
 */

namespace App\Service;

use Geocoder\Provider\Chain\Chain;
use Geocoder\Provider\GoogleMaps\GoogleMaps;
use Geocoder\Provider\Here\Here;
use Geocoder\ProviderAggregator;
use Geocoder\Query\GeocodeQuery;
use Geocoder\Query\ReverseQuery;
use GuzzleHttp\Client;

class GeoCodeService
{
    /*private Here $hereProvider;
    private GoogleMaps $googleMapsProvider;
    private Chain $chainProvider;
    private ProviderAggregator $providerAgregator;
    private GeocodeQuery $geocodeQuery;
    private ReverseQuery $reverseQuery;
    private Client $guzzleClient;*/
    protected static $geocoder;

    /*public function __construct(ProviderAggregator $providerAggregator, GeocodeQuery $geocodeQuery, ReverseQuery $reverseQuery, Chain $chain, Here $here, GoogleMaps $googleMaps, Client $client)
    {
        $this->providerAgregator = $providerAggregator;
        $this->geocodeQuery = $geocodeQuery;
        $this->reverseQuery = $reverseQuery;
        $this->chainProvider = $chain;
        $this->hereProvider = $here;
        $this->googleMapsProvider = $googleMaps;
        $this->guzzleClient = $client;
    }*/

    static protected function getGeoCoder()
    {
        if ( !self::$geocoder ) {
            self::$geocoder = new ProviderAggregator();
            $adapter = new Client();
            $chain = new Chain([
                new Here($adapter, 'eoQm7snFfZ4TZOd2gnPT', 'vJcc4TntD70lhe31n-HMpQ'),
                new GoogleMaps($adapter, null, 'AIzaSyC983NJ9_Ub2jFbQ6mwdqlT8tGVjnmxhJQ')
            ]);
            self::$geocoder->registerProvider($chain);
        }
        return self::$geocoder;
    }

    public static function getLatLong(string $address)
    {
        $result = self::getGeoCoder()->geocodeQuery(GeocodeQuery::create($address));
        if ( !$result->isEmpty() ) {
            return [ 'lat' => $result->first()->getCoordinates()->getLatitude(), 'lng' => $result->first()->getCoordinates()->getLongitude() ];
        }
        return null;
    }

    public static function getAddress(string $lat, string $lng)
    {
        $result = self::getGeoCoder()->reverseQuery(ReverseQuery::fromCoordinates($lat, $lng));
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