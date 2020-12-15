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
        $daysOnTheMarket = $this->getListingDaysOnTheMarket($listing->getRawData()[ 'ListingContractDate' ]);
        $listingObject = (object)[
            'mlsNumber'       => $listing->getMlsNum(),
            'listingId'       => $listing->getFeedListingID(),
            'feedId'          => $listing->getFeedID(),
            'type'            => $listing->getType(),
            'ownershipType'   => $listing->getOwnershipType(),
            'images'          => $listingImagesUrlArray,
            'coordinates'     => $this->getSingleListingCoordinatesObject($listing),
            'daysOnTheMarket' => $daysOnTheMarket,
            'description'     => $listing->getRawData()[ 'PublicRemarks' ],
            'address'         => $this->getListingAddressObject($listing),
            'metrics'         => $this->getListingMetricsObject($listing),
            'financials'      => $this->getListingFinancialsObject($listing),
            'listingAgent'    => $this->getListingAgentObject($listing),
            'agent'           => $this->getAgentObject(),
            'isNew'           => $daysOnTheMarket <= 3,
            'listingSeo'      => $this->getListingSeoObject($listing),
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
            'country'       => $listing->getCountry(),
            'state'         => $listing->getStateOrProvince(),
            'city'          => $listing->getCity(),
            'postalCode'    => $listing->getPostalCode(),
            'streetAddress' => $listing->getUnparsedAddress(),
        ];
    }

    private function getListingLotSize(Listing $listing): ?int
    {
        if ( !is_null($listing->getLotSize()) || $listing->getLotSize() != 0 ) {
            return (int)$listing->getLotSize();
        }
        return null;
    }

    private function getListingBuildingAreaTotal(Listing $listing): ?int
    {
        if ( !is_null($listing->getLivingArea()) || $listing->getLivingArea() != 0 ) {
            return (int)$listing->getLivingArea();
        }
        return null;
    }

    private function getListingFinancialsObject(Listing $listing): object
    {
        return (object)[
            'listingPrice'         => $listing->getListPrice(),
            'strataMaintenanceFee' => $listing->getRawData()[ 'AssociationFee' ],
            'grossTaxes'           => null,
            'grossTaxYear'         => null,
            'originalListingPrice' => $listing->getOriginalPrice(),
        ];
    }

    private function getListingMetricsObject(Listing $listing): object
    {
        return (object)[
            'yearBuilt'        => $listing->getYearBuilt(),
            'bedRooms'         => $listing->getBedrooms(),
            'bathRooms'        => (int)$listing->getRawData()[ 'BathroomsTotal' ],
            'stories'          => (int)$listing->getRawData()[ 'Stories' ],
            'lotSize'          => $this->getListingLotSize($listing),
            'lotSizeUnits'     => $listing->getRawData()[ 'LotSizeUnits' ],
            'sqrtFootage'      => $this->getListingBuildingAreaTotal($listing),
            'sqrtFootageUnits' => $listing->getRawData()[ 'BuildingAreaUnits' ],
        ];
    }

    private function getListingAgentObject(Listing $listing): object
    {
        return (object)[
            'agentFullName' => $listing->getRawData()[ 'ListAgentFullName' ],
            'agencyName'    => $listing->getRawData()[ 'ListOfficeName' ],
            'agentPhone'    => $listing->getRawData()[ 'ListAgentOfficePhone' ],
            'agentEmail'    => $listing->getRawData()[ 'ListAgentEmail' ],
        ];
    }

    private function getListingSeoObject(Listing $listing): object
    {
        return (object)[
            'browserTitle'       => '',
            'pageTitle'          => '',
            'metaKeywords'       => '',
            'metaDescription'    => '',
            'description'        => $this->getListingSeoDescription($listing),
            'sitemapDescription' => '',
        ];
    }

    private function getListingSeoDescription(Listing $listing): string
    {
        $shortCodesData = [
            'AddressFull'                            => $listing->getFullAddress(),
            'SubType'                                => $listing->getType(),
            'ListPrice'                              => $listing->getListPrice(),
            'Beds'                                   => $listing->getBedrooms(),
            'Baths'                                  => $listing->getRawData()[ 'BathroomsTotal' ],
            'FloorArea'                              => $listing->getLivingArea(),
            'YearBuilt'                              => $listing->getYearBuilt(),
            'MLS'                                    => $listing->getMlsNum(),
            'City'                                   => $listing->getCity(),
            'DayOnMarket'                            => $this->getListingDaysOnTheMarket($listing->getRawData()[ 'ListingContractDate' ]),
            'LblPrice'                               => '',
            'MedianListCityPrice'                    => '',
            'MedianCityPriceStatus'                  => '',
            'PrivateSchLbl'                          => '',
            'ClosesteleIndSchool'                    => '',
            'SchoolRating'                           => '',
            'IndSchDistance'                         => '',
            'ClosestSecPrivateSchool'                => '',
            'PrivateSchoolRating'                    => '',
            'SecPrivateSchDistance'                  => '',
            'TransitLbl'                             => '',
            'Condition'                              => '',
            'Street'                                 => '',
            'SkytrainStationName'                    => '',
            'SkytrainStationLine'                    => '',
            'SkytrainDistance'                       => '',
            'BusStationName'                         => '',
            'BusDistance'                            => '',
            'EducationLbl'                           => '',
            'TotalPeopleWithDegree'                  => '',
            'DemographicsArea'                       => '',
            'DegreeStatus'                           => '',
            'TotalPeopleWithoutDegree'               => '',
            'NoDegreeStatus'                         => '',
            'IncomeLbl'                              => '',
            'MedianHouseHoldIncome'                  => '',
            'MedianIncomeStatus'                     => '',
            'UnemploymentLbl'                        => '',
            'UnemploymentRate'                       => '',
            'UnemploymentStatus'                     => '',
            'ClimateLbl'                             => '',
            'ClosestWeatherStation'                  => '',
            'ClosestWeatherStationDistance'          => '',
            'ClosestWeatherStationElevation'         => '',
            'ClosestWeatherStationTemperature'       => '',
            'ClosestWeatherStationTemperatureStatus' => '',
            'ClosestWeatherStationRainfallRate'      => '',
            'ClosestWeatherStationRainfallStatus'    => '',
            'ClosesWeatherStationSnowfallRate'       => '',
            'ClosesWeatherStationSnowFallStatus'     => '',
            'FloodAreaLbl'                           => '',
            'FloodStatus'                            => '',
            'CemeteriesLbl'                          => '',
            'Cemeteries'                             => '',
            'ALRLbl'                                 => '',
            'ALRStatus'                              => '',
            'PopulationChangeLbl'                    => '',
            'PopulationChange'                       => '',
            'PopulationChangeStatus'                 => '',
            'AverageChildrenLbl'                     => '',
            'ChildrenRate'                           => '',
            'ChildrenStatus'                         => '',
            'MedianAgeLbl'                           => '',
            'MedianAgeRate'                          => '',
            'MedianAgeStatus'                        => '',
            'PopulationDensityLbl'                   => '',
            'PopulationDensityStatus'                => '',
            'PopulationDensityRate'                  => '',
            'SinglesLbl'                             => '',
            'SinglesStatus'                          => '',
            'SinglesRate'                            => '',
            'CitizenshipLbl'                         => '',
            'CitizenshipRate'                        => '',
            'CitizenshipStatus'                      => '',
            'RentLbl'                                => '',
            'GrossRentStatus'                        => '',
            'RentRate'                               => '',
            'GrossRentAmount'                        => '',
            'RentStatus'                             => '',
            'AverageOwnerPaymentsLbl'                => '',
            'AverageOwnerPaymentsStatus'             => '',
            'AverageOwnerPaymentsCount'              => '',
            'TransportLbl'                           => '',
            'PublicTransportRate'                    => '',
            'WalkingBicycleRate'                     => '',
            'AddressSmall'                           => '',
            'Location'                               => '',
        ];
        $seoDescription = "
            <p>313 4360 Lorimer Rd, Whistler, BC - V8E 1A5 is a Apartment/Condo that currently for sale for $699999 with 1 bedrooms and 1 bathrooms, with 547 sq.ft living area. It was built in 1997. It was listed in MLS® under # R2468653 and available for 147 days on Estateblock.com. This listing is located in Whistler Village.</p>
            <p>Apartment/Condo for sale is situated in Whistler Village in Whistler.</p>
            <p>Price : The Median List price for the property is 0.00% than comparables in the city and 0.00% than similar homes in the neighbourhood.</p>
            <p>Public Schools : This home is serviced by Myrtle Philip Community School. The property is located 1.73 km from the school. Myrtle Philip Community School has a rating of 7/10. This home is also serviced by Whistler Secondary and located 5.59 km from it. Whistler Secondary has a rating of 8/10.</p>
            <p>Private Schools : The closest elementary independent school with good rating is Collingwood School. The School's rating is 10/10 and it is located 137.29 km.</p>
            <p>The closest Secondary Private School with good rating is Collingwood School. The School's rating is 10/10. The school is located 137.29 km from it.</p>
            <p>Total Crime : The freshest crime data for BC municipalities from Statistics Canada are from 2015. There were 1334 crime incidents excluding traffic incidents. The overall crime rate (excluding traffic) is 12236.29. Rate is the amount of incidents per 100,000 population. Comparing to other South West BC cities it has a High rate in 2015.</p>
            <p>Drug Crime : Drug crime rate is High comparing to other neighbourhoods. The drug crime rate is 2375.71. This is the amount of</p>
            
            <p>313 4360 Lorimer Rd, Whistler, BC - V8E 1A5 is a Apartment/Condo that currently for sale for $699999 with 1 bedrooms and 1 bathrooms, with 547 sq.ft living area. It was built in 1997. It was listed in MLS® under # R2468653 and available for 147 days on Estateblock.com. This listing is located in Whistler Village.</p>
            <p>Apartment/Condo for sale is situated in Whistler Village in Whistler.</p>
            <p>Price : The Median List price for the property is 0.00% than comparables in the city and 0.00% than similar homes in the neighbourhood.</p>
            <p>Public Schools : This home is serviced by Myrtle Philip Community School. The property is located 1.73 km from the school. Myrtle Philip Community School has a rating of 7/10. This home is also serviced by Whistler Secondary and located 5.59 km from it. Whistler Secondary has a rating of 8/10.</p>
            <p>Private Schools : The closest elementary independent school with good rating is Collingwood School. The School's rating is 10/10 and it is located 137.29 km.</p>
            <p>The closest Secondary Private School with good rating is Collingwood School. The School's rating is 10/10. The school is located 137.29 km from it.</p>
            <p>Total Crime : The freshest crime data for BC municipalities from Statistics Canada are from 2015. There were 1334 crime incidents excluding traffic incidents. The overall crime rate (excluding traffic) is 12236.29. Rate is the amount of incidents per 100,000 population. Comparing to other South West BC cities it has a High rate in 2015.</p>
            <p>Drug Crime : Drug crime rate is High comparing to other neighbourhoods. The drug crime rate is 2375.71. This is the amount of</p>
            
            <p>313 4360 Lorimer Rd, Whistler, BC - V8E 1A5 is a Apartment/Condo that currently for sale for $699999 with 1 bedrooms and 1 bathrooms, with 547 sq.ft living area. It was built in 1997. It was listed in MLS® under # R2468653 and available for 147 days on Estateblock.com. This listing is located in Whistler Village.</p>
            <p>Apartment/Condo for sale is situated in Whistler Village in Whistler.</p>
            <p>Price : The Median List price for the property is 0.00% than comparables in the city and 0.00% than similar homes in the neighbourhood.</p>
            <p>Public Schools : This home is serviced by Myrtle Philip Community School. The property is located 1.73 km from the school. Myrtle Philip Community School has a rating of 7/10. This home is also serviced by Whistler Secondary and located 5.59 km from it. Whistler Secondary has a rating of 8/10.</p>
            <p>Private Schools : The closest elementary independent school with good rating is Collingwood School. The School's rating is 10/10 and it is located 137.29 km.</p>
            <p>The closest Secondary Private School with good rating is Collingwood School. The School's rating is 10/10. The school is located 137.29 km from it.</p>
            <p>Total Crime : The freshest crime data for BC municipalities from Statistics Canada are from 2015. There were 1334 crime incidents excluding traffic incidents. The overall crime rate (excluding traffic) is 12236.29. Rate is the amount of incidents per 100,000 population. Comparing to other South West BC cities it has a High rate in 2015.</p>
            <p>Drug Crime : Drug crime rate is High comparing to other neighbourhoods. The drug crime rate is 2375.71. This is the amount of</p>
        ";
        $keys = array_keys($shortCodesData);
        $values = array_values($shortCodesData);
        $keys = array_map(function($item) {
            return '/\[' . $item . '\]/';
        }, $keys);
        $seoDescription = preg_replace($keys, $values, $seoDescription);
        return $seoDescription;
    }

    private function getAgentObject()
    {
        return (object)[
            'agentName'  => 'Dan Marusin',
            'agencyName' => 'Renanza Realty',
            'agentPhone' => '778-200-2710',
            'agentEmail' => 'vadim@estateblock.com',
            'agentPhoto' => $_ENV[ 'ESBL_DIGITAL_OCEAN_ENDPOINT_EDGE' ] . '/agents/' . 'dan_marusin.jpg',
        ];
    }

}