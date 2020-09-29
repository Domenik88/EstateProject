<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 29.09.2020
 *
 * @package estateblock20
 */

namespace App\Service\Listing;


use App\Entity\Listing;
use App\Service\Geo\GeoCodeService;
use App\Service\Geo\Point;

class ListingGeoService
{
    private GeoCodeService $geoCodeService;
    private ListingService $listingService;

    public function __construct(GeoCodeService $geoCodeService, ListingService $listingService)
    {
        $this->geoCodeService = $geoCodeService;
        $this->listingService = $listingService;
    }

    public function syncListingCoordinatesFromAddress(Listing $listing)
    {
        $listingAddress = $listing->getUnparsedAddress();
        $listingCoordinates = $this->geoCodeService->getLatLong($listingAddress);
        $this->listingService->setListingCoordinates($listing, new Point($listingCoordinates['lat'],$listingCoordinates['lng']));
    }
}