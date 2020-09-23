<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 23.09.2020
 *
 * @package estateblock20
 */

namespace App\Service\Provider;


use Geocoder\Provider\Chain\Chain;
use Geocoder\Provider\GoogleMaps\GoogleMaps;
use Geocoder\Provider\Here\Here;
use Geocoder\ProviderAggregator;
use GuzzleHttp\Client;

class GeoCoderProvider
{
    private ProviderAggregator $geocoder;

    public function getGeoCoder()
    {
        if ( $this->geocoder ) {
            $this->geocoder = new ProviderAggregator();
            $adapter = new Client();
            $chain = new Chain([
                new Here($adapter, 'eoQm7snFfZ4TZOd2gnPT', 'vJcc4TntD70lhe31n-HMpQ'),
                new GoogleMaps($adapter, null, 'AIzaSyC983NJ9_Ub2jFbQ6mwdqlT8tGVjnmxhJQ')
            ]);
            $this->geocoder->registerProvider($chain);
        }
        return $this->geocoder;
    }

}