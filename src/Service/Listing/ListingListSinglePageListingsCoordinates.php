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
    private ListingFullUnparsedAddressService $listingFullUnparsedAddressService;

    public function __construct(ListingFullUnparsedAddressService $listingFullUnparsedAddressService)
    {
        $this->listingFullUnparsedAddressService = $listingFullUnparsedAddressService;
    }

    public function getListingListCoordinates(array $listingListSinglePage)
    {
        $coordinatesList = [];
        $counter = 0;
        foreach ($listingListSinglePage as $listing) {
            $coordinatesList[$counter]['mlsNum'] = $listing->getMlsNum();
            $coordinatesList[$counter]['address'] = $this->listingFullUnparsedAddressService->getListingFullUnparsedAddress($listing);
            $coordinatesList[$counter]['lat'] = $listing->getCoordinates()->getLatitude();
            $coordinatesList[$counter]['lng'] = $listing->getCoordinates()->getLongitude();
            $counter++;
        }
        return $coordinatesList;
    }
}