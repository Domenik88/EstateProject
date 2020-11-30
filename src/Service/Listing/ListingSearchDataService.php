<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 29.11.2020
 *
 * @package estateblock20
 */

namespace App\Service\Listing;

use App\Entity\Listing;
use DateTime;

class ListingSearchDataService
{
    private ListingMediaService $listingMediaService;

    public function __construct(ListingMediaService $listingMediaService)
    {
        $this->listingMediaService = $listingMediaService;
    }

    public function constructSearchListingData(Listing $listing): object
    {
        $listingImagesUrlArray = $this->listingMediaService->getListingPhotos($listing);

        $listingObject = (object)[
            'yearBuilt' => $listing->getYearBuilt(),
            'mlsNumber' => $listing->getMlsNum(),
            'feedId' => $listing->getFeedID(),
            'type' => $listing->getType(),
            'ownershipType' => $listing->getOwnershipType(),
            'images' => $listingImagesUrlArray,
            'coordinates' => $this->getSingleListingCoordinatesObject($listing),
            'daysOnTheMarket' => $this->getListingDaysOnTheMarket($listing->getRawData()['ListingContractDate']),
            'description' => $listing->getRawData()['PublicRemarks'],
            'address' => $this->getListingAddressObject($listing),
            'metrics' => $this->getListingMetricsObject($listing),
            'financials' => $this->getListingFinancialsObject($listing),
            'listingAgent' => $this->getListingAgentObject($listing),
        ];

        return $listingObject;
    }

    private function getSingleListingCoordinatesObject(Listing $listing): object
    {
        return (object)[
            'lat' => $listing->getCoordinates()->getLatitude(),
            'lng' => $listing->getCoordinates()->getLongitude(),
        ];
    }

    private function getListingDaysOnTheMarket($listingContractDate): int
    {
        return date_diff(new DateTime(), new DateTime($listingContractDate))->days;
    }

    private function getListingAddressObject(Listing $listing): object
    {
        return (object)[
            'bedRooms' => $listing->getBedrooms(),
            'bathRooms' => (int)$listing->getRawData()['BathroomsTotal'],
            'stories' => (int)$listing->getRawData()['Stories'],
            'lotSize' => $this->getListingLotSize($listing),
            'lotSizeUnits' => $listing->getRawData()['LotSizeUnits'],
            'sqrtFootage' => $this->getListingBuildingAreaTotal($listing),
            'sqrtFootageUnits' => $listing->getRawData()['BuildingAreaUnits'],
        ];
    }

    private function getListingLotSize(Listing $listing): ?int
    {
        if (!is_null($listing->getLotSize()) || $listing->getLotSize() != 0) {
            return (int)$listing->getLotSize();
        }

        return null;
    }

    private function getListingBuildingAreaTotal(Listing $listing): ?int
    {
        if (!is_null($listing->getLivingArea()) || $listing->getLivingArea() != 0) {
            return (int)$listing->getLivingArea();
        }

        return null;
    }

    private function getListingFinancialsObject(Listing $listing): object
    {
        return (object)[
            'listingPrice' => $listing->getListPrice(),
            'strataMaintenanceFee' => 'N/A',
            'grossTaxes' => 'N/A',
            'grossTaxYear' => 'N/A',
            'originalListingPrice' => $listing->getListPrice(),
        ];
    }

    private function getListingMetricsObject(Listing $listing): object
    {
        return (object)[
            'bedRooms' => $listing->getBedrooms(),
            'bathRooms' => (int)$listing->getRawData()['BathroomsTotal'],
            'stories' => (int)$listing->getRawData()['Stories'],
            'lotSize' => $this->getListingLotSize($listing),
            'lotSizeUnits' => $listing->getRawData()['LotSizeUnits'],
            'sqrtFootage' => $this->getListingBuildingAreaTotal($listing),
            'sqrtFootageUnits' => $listing->getRawData()['BuildingAreaUnits'],
        ];
    }

    private function getListingAgentObject(Listing $listing): object
    {
        return (object)[
            'agentFullName' => $listing->getRawData()['ListAgentFullName'],
            'agencyName' => $listing->getRawData()['ListOfficeName'],
            'agentPhone' => $listing->getRawData()['ListAgentOfficePhone'],
            'agentEmail' => $listing->getRawData()['ListAgentEmail'],
        ];
    }

}