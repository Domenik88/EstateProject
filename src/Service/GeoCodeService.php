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
    private Here $hereProvider;
    private GoogleMaps $googleMapsProvider;
    private Chain $chainProvider;
    private ProviderAggregator $providerAgregator;
    private GeocodeQuery $geocodeQuery;
    private ReverseQuery $reverseQuery;
    private Client $guzzleClient;
    protected static $geocoder;

    public function __construct(ProviderAggregator $providerAggregator, GeocodeQuery $geocodeQuery, ReverseQuery $reverseQuery, Chain $chain, Here $here, GoogleMaps $googleMaps, Client $client)
    {
        $this->providerAgregator = $providerAggregator;
        $this->geocodeQuery = $geocodeQuery;
        $this->reverseQuery = $reverseQuery;
        $this->chainProvider = $chain;
        $this->hereProvider = $here;
        $this->googleMapsProvider = $googleMaps;
        $this->guzzleClient = $client;
    }

    private function getGeoCoder()
    {
        if (!self::$geocoder){
            self::$geocoder = $this->providerAgregator;
            $chain = $this->chainProvider;
        }
    }
}