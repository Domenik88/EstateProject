<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 09.10.2020
 *
 * @package estateblock20
 */

namespace App\Service\Listing;



class ListingListSinglePageListingsCoordinates
{

    public function getListingListCoordinates(array $listingListSinglePage): array
    {
        $coordinatesList = [];
        $counter = 0;
        foreach ($listingListSinglePage as $listing) {
            $coordinatesList[$counter]['mlsNum'] = $listing->getMlsNum();
            $coordinatesList[$counter]['address'] = $listing->getFullAddress();
            $coordinatesList[$counter]['lat'] = $listing->getCoordinates()->getLatitude();
            $coordinatesList[$counter]['lng'] = $listing->getCoordinates()->getLongitude();
            $counter++;
        }
        return $coordinatesList;
    }
}