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
use App\Entity\User;
use DateTime;
use Symfony\Component\Security\Core\Security;

class ListingSearchDataService
{
    private ListingMediaService $listingMediaService;
    private Security $security;

    public function __construct(ListingMediaService $listingMediaService, Security $security)
    {
        $this->listingMediaService = $listingMediaService;
        $this->security = $security;
    }

    public function constructSearchListingData(Listing $listing): object
    {
        $currentUser = $this->security->getUser();
        if ($currentUser instanceof User) {
            $userFavorite = $this->security->getUser()->getFavoriteListings()->contains($listing);
        } else {
            $userFavorite = false;
        }

        $listingImagesUrlArray = $this->listingMediaService->getListingPhotos($listing);
        $daysOnTheMarket = $this->getListingDaysOnTheMarket($listing->getContractDate());
        $listingObject = (object)[
            'mlsNumber'        => $listing->getMlsNum(),
            'listingFeedId'    => $listing->getFeedListingID(),
            'listingId'        => $listing->getId(),
            'feedId'           => $listing->getFeedID(),
            'type'             => $listing->getType(),
            'ownershipType'    => $listing->getOwnershipType(),
            'images'           => $listingImagesUrlArray,
            'coordinates'      => $this->getSingleListingCoordinatesObject($listing),
            'daysOnTheMarket'  => $daysOnTheMarket,
            'description'      => array_key_exists('PublicRemarks', $listing->getRawData()) ? $listing->getRawData()[ 'PublicRemarks' ] : null,
            'address'          => $this->getListingAddressObject($listing),
            'metrics'          => $this->getListingMetricsObject($listing),
            'financials'       => $this->getListingFinancialsObject($listing),
            'listingAgent'     => $this->getListingAgentObject($listing),
            'agent'            => $this->getAgentObject(),
            'isNew'            => !is_null($daysOnTheMarket) ? $daysOnTheMarket <= 3 : false,
            'listingSeo'       => $this->getListingSeoObject($listing),
            'status'           => $listing->getStatus(),
            'processingStatus' => $listing->getProcessingStatus(),
            'selfListing'      => $listing->getSelfListing(),
            'contractDate'     => $listing->getContractDate(),
            'userFavorite'     => $userFavorite,
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

    private function getListingDaysOnTheMarket($listingContractDate): ?int
    {
        if ( !is_null($listingContractDate) ) {
            return date_diff(new DateTime(), $listingContractDate)->days;
        } else {
            return null;
        }
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
            'strataMaintenanceFee' => array_key_exists('AssociationFee', $listing->getRawData()) ? $listing->getRawData()[ 'AssociationFee' ] : null,
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
            'bathRooms'        => array_key_exists('BathroomsTotal', $listing->getRawData()) ? (int)$listing->getRawData()[ 'BathroomsTotal' ] : null,
            'stories'          => array_key_exists('Stories', $listing->getRawData()) ? (int)$listing->getRawData()[ 'Stories' ] : null,
            'lotSize'          => $this->getListingLotSize($listing),
            'lotSizeUnits'     => array_key_exists('LotSizeUnits', $listing->getRawData()) ? $listing->getRawData()[ 'LotSizeUnits' ] : null,
            'sqrtFootage'      => $this->getListingBuildingAreaTotal($listing),
            'sqrtFootageUnits' => array_key_exists('BuildingAreaUnits', $listing->getRawData()) ? $listing->getRawData()[ 'BuildingAreaUnits' ] : null,
        ];
    }

    private function getListingAgentObject(Listing $listing): object
    {
        return (object)[
            'agentFullName' => array_key_exists('ListAgentFullName', $listing->getRawData()) ? $listing->getRawData()[ 'ListAgentFullName' ] : null,
            'agencyName'    => array_key_exists('ListOfficeName', $listing->getRawData()) ? $listing->getRawData()[ 'ListOfficeName' ] : null,
            'agentPhone'    => array_key_exists('ListAgentOfficePhone', $listing->getRawData()) ? $listing->getRawData()[ 'ListAgentOfficePhone' ] : null,
            'agentEmail'    => array_key_exists('ListAgentEmail', $listing->getRawData()) ? $listing->getRawData()[ 'ListAgentEmail' ] : null,
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
            'Baths'                                  => array_key_exists('BathroomsTotal', $listing->getRawData()) ? $listing->getRawData()[ 'BathroomsTotal' ] : '',
            'FloorArea'                              => $listing->getLivingArea(),
            'YearBuilt'                              => $listing->getYearBuilt(),
            'MLS'                                    => $listing->getMlsNum(),
            'City'                                   => $listing->getCity(),
            'DayOnMarket'                            => $this->getListingDaysOnTheMarket($listing->getContractDate()),
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
            <p>[AddressFull] is a [SubType] that currently for sale for $[ListPrice] with [Beds] bedrooms and [Baths] bathrooms, with [FloorArea] sq.ft living area. It was built in [YearBuilt]. It was listed in MLS® under # [MLS]. This listing is located in [City].</p>
            <p>[LblPrice] The Median List price for the property is [MedianListCityPrice]% [MedianCityPriceStatus] than comparables in the city.</p>
            <p>[PrivateSchLbl] The closest elementary independent school with good rating is [ClosesteleIndSchool]. The School's rating is [SchoolRating]/10 and it is located [IndSchDistance] km.</p>
            <p>The closest Secondary Private School with good rating is [ClosestSecPrivateSchool]. The School's rating is [PrivateSchoolRating]/10. The school is located [SecPrivateSchDistance] km from it.</p>
            <p>[TransitLbl] [Start Condition]The closest Skytrain/Railway station to [Street], [City] is [SkytrainStationName] on [SkytrainStationLine] line in [SkytrainDistance] km from this home. [End Condition]The closest Bus station is [BusStationName] in [BusDistance] km away.</p>
            <p>[EducationLbl] [TotalPeopleWithDegree]% of people with university certificate/degree live in [DemographicsArea] which is [DegreeStatus] in comparison with the BC average. [TotalPeopleWithoutDegree]% of population in [DemographicsArea] have no certificate or degree. This number is [NoDegreeStatus] compared to the average in British Columbia.</p>
            <p>[IncomeLbl] Median Household Income near this dwelling in [DemographicsArea] is [MedianHouseHoldIncome] which is [MedianIncomeStatus] in comparison with the BC average.</p>
            <p>[UnemploymentLbl] Unemployment rate in the area around Listing # [MLS] is [UnemploymentRate] which is [UnemploymentStatus] in comparison to the other British Columbia neighbourhoods.</p>
            <p>[ClimateLbl] The closest weather station is [ClosestWeatherStation]. It is located [ClosestWeatherStationDistance]km away. Weather station elevation is [ClosestWeatherStationElevation]m.</p>  
            <p>Daily average temperature around this station is [ClosestWeatherStationTemperature]C which is [ClosestWeatherStationTemperatureStatus] compared to the  local average.</p> 
            <p>Rainfall is about [ClosestWeatherStationRainfallRate]mm yearly which is [ClosestWeatherStationRainfallStatus] compared to the local averages.</p>
            <p>Snowfall is [ClosesWeatherStationSnowfallRate]mm yearly. This number is [ClosesWeatherStationSnowFallStatus] compared to other local neighbourhoods.</p>
            <p>[FloodAreaLbl] According to an official flood area map [Street] home for sale is [FloodStatus] . This information is for general informational purposes only. You should not use such information in determining the chances of this house being flooded.</p>
            <p>[CemeteriesLbl]  [MLS] is [Cemeteries].</p>
            <p>[ALRLbl]  [SubType] [Street] [ALRStatus].</p>
            <p>[PopulationChangeLbl] This real estate is located in Subdivision where population change between 2006 and 2011 was [PopulationChange]%. This is [PopulationChangeStatus] in comparison to average growth rate of this region.</p>
            <p>[AverageChildrenLbl]  Average Number of Children in Subdivision is [ChildrenRate]. This is [ChildrenStatus] number in comparison to the whole region.</p>
            <p>[MedianAgeLbl]  [MedianAgeRate]% of population in this area is 65 and over. This percentage is [MedianAgeStatus] in comparison to other BC cities.</p>
            <p>[PopulationDensityLbl]  Population Density in the area is [PopulationDensityStatus]. [PopulationDensityRate] people per sq.km.</p> 
            <p>[SinglesLbl] [Street], [City] property for sale is situated in the area with a/an [SinglesStatus] percentage of singles, [SinglesRate]%.</p>
            <p>[CitizenshipLbl] [AddressFull], MLS #[MLS] is situated in [DemographicsArea], [City]. [CitizenshipRate]% are Canadian Citizens in this neighbourhood, which is [CitizenshipStatus] in comparison to local rate.</p>
            <p>[RentLbl] This [City] home for sale is in the neighbourhood with a/an [GrossRentStatus] amount of rented dwellings. [RentRate]% of all dwellings are currently occupied by renters. Average gross rent is $[GrossRentAmount] which is [RentStatus] compared to the local average</p>
            <p>[AverageOwnerPaymentsLbl]  Average Owner payments are [AverageOwnerPaymentsStatus] in this area. $[AverageOwnerPaymentsCount] owners spent in average for the dwelling.</p>
            <p>[TransportLbl]  [PublicTransportRate]% of all population in the area around this real estate using Public Transport and only [WalkingBicycleRate]% walking and using bicycle.</p>
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