<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 19.01.2021
 *
 * @package estateblock20
 */

namespace App\Service\Geo;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class HereRouteService
{
    public function getRoute(Point $origin, Point $destination)
    {
        $client = new Client();
        return $client->request('GET', 'https://route.api.here.com/routing/7.2/calculateroute.json', [
            RequestOptions::QUERY => [
                'app_id' => 'eoQm7snFfZ4TZOd2gnPT',
                'app_code' => 'vJcc4TntD70lhe31n-HMpQ',
                'waypoint0' => 'geo!' . $origin->getLatitude() . ',' . $origin->getLongitude(),
                'waypoint1' => 'geo!' . $destination->getLatitude() . ',' . $destination->getLongitude(),
                'mode' => 'fastest;car;traffic:disabled',
            ],
        ]);
    }
}