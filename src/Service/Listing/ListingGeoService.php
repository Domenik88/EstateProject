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
use Psr\Log\LoggerInterface;

class ListingGeoService
{
    private GeoCodeService $geoCodeService;
    private ListingService $listingService;
    private LoggerInterface $logger;
    private ListingFullUnparsedAddressService $listingFullUnparsedAddressService;

    public function __construct(GeoCodeService $geoCodeService, ListingService $listingService, LoggerInterface $logger, ListingFullUnparsedAddressService $listingFullUnparsedAddressService)
    {
        $this->geoCodeService = $geoCodeService;
        $this->listingService = $listingService;
        $this->logger = $logger;
        $this->listingFullUnparsedAddressService = $listingFullUnparsedAddressService;
    }

    public function syncListingCoordinatesFromAddress(Listing $listing)
    {
        if ( is_null($listing->getCoordinates()->getLongitude()) or is_null($listing->getCoordinates()->getLatitude())) {
            $listingAddress = $this->listingFullUnparsedAddressService->getListingFullUnparsedAddress($listing);
            $listingCoordinates = $this->geoCodeService->getLatLong($listingAddress);
            if ( is_null($listingCoordinates) ) {
                throw new \Exception("Coordinates not found for Listing {$listing->getMlsNum()} feed {$listing->getFeedID()}");
            }
            $this->listingService->setListingCoordinates($listing, new Point($listingCoordinates['lat'], $listingCoordinates['lng']));
        }
    }

}