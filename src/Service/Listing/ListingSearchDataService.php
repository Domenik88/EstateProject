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
            'yearBuilt'             => $listing->getYearBuilt(),
            'mlsNumber'             => $listing->getMlsNum(),
            'feedId'                => $listing->getFeedID(),
            'type'                  => $listing->getType(),
            'ownershipType'         => $listing->getOwnershipType(),
            'images'                => $listingImagesUrlArray,
            'coordinates'           => $this->getSingleListingCoordinatesObject($listing),
            'daysOnTheMarket'       => $this->getListingDaysOnTheMarket($listing->getRawData()[ 'ListingContractDate' ]),
            'description'           => $listing->getRawData()[ 'PublicRemarks' ],
            'address'               => $this->getListingAddressObject($listing),
            'metrics'               => $this->getListingMetricsObject($listing),
            'financials'            => $this->getListingFinancialsObject($listing),
            'listingAgent'          => $this->getListingAgentObject($listing),
            'listingSeoDescription' => $this->getListingSeoDescription($listing),
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
            'bedRooms'         => $listing->getBedrooms(),
            'bathRooms'        => (int)$listing->getRawData()[ 'BathroomsTotal' ],
            'stories'          => (int)$listing->getRawData()[ 'Stories' ],
            'lotSize'          => $this->getListingLotSize($listing),
            'lotSizeUnits'     => $listing->getRawData()[ 'LotSizeUnits' ],
            'sqrtFootage'      => $this->getListingBuildingAreaTotal($listing),
            'sqrtFootageUnits' => $listing->getRawData()[ 'BuildingAreaUnits' ],
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
            'strataMaintenanceFee' => 'N/A',
            'grossTaxes'           => 'N/A',
            'grossTaxYear'         => 'N/A',
            'originalListingPrice' => $listing->getListPrice(),
        ];
    }

    private function getListingMetricsObject(Listing $listing): object
    {
        return (object)[
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

    private function getListingSeoDescription(Listing $listing): string
    {
        $seoDescriptionNotParsed = "[AddressFull] is a [SubType] that currently for sale for $[ListPrice] with [Beds] bedrooms and [Baths] bathrooms, with [FloorArea] sq.ft living area. It was built in [YearBuilt]. It was listed in MLS® under # [MLS] and available for [DayOnMarket] days on Estateblock.com. This listing is located in [Subdivision].

[SubType] for sale is situated in [SubAreaCommunity] in [Area].
[Condos|BuildingText]
[LblPrice] The Median List price for the property is [MedianListCityPrice]% [MedianCityPriceStatus] than comparables in the city and [MedianListNeighbourhoodPrice]% [MedianCommPriceStatus] than similar homes in the neighbourhood. 
~~~~~
[PublicSchLbl] This home is serviced by [ElementarySchoolname]. The property is located [ElementryDistance] km from the school. [ElementarySchoolname] has a rating of [ElementryRate]/10. This home is also serviced by [SecondarySchoolName] and located [SecondaryDistance] km from it. [SecondarySchoolName] has a rating of [SecondaryRating]/10.

[PrivateSchLbl] The closest elementary independent school with good rating is [ClosesteleIndSchool]. The School's rating is [SchoolRating]/10 and it is located [IndSchDistance] km.

The closest Secondary Private School with good rating is [ClosestSecPrivateSchool]. The School's rating is [PrivateSchoolRating]/10. The school is located [SecPrivateSchDistance] km from it.

[TotalCrimeLbl]  The freshest crime data for BC municipalities from Statistics Canada are from 2015. There were [AllIncidents] crime incidents excluding traffic incidents.  The overall crime rate (excluding traffic) is [AllRate]. Rate is the amount of incidents per 100,000 population.  Comparing to other South West BC cities it has a [AllRating] rate in 2015.
 
[DrugCrimeLbl] Drug crime rate is [DrugRating] comparing to other neighbourhoods. The drug crime rate is [DrugRate]. This is the amount of drug crime incidents per 100,000 population.  There were [DrugIncidents] drug crime incidents in this city in 2015.
  
[PropertyCrimeLbl] Property crimes are [PropertyRating] in the city. Property crime rate is [PropertyRate] which is [PropertyRating] in comparison to other Lower Mainland and surroundings. There were [PropertyIncidents] in the neighbourhood in 2015. 

[ViolentCrimeLbl] There were [ViolentIncidents] violent crime incidents in 2015. The violent crime rate per 100,000 population was [ViolentRate] and it is [ViolentRating] rate comparing to other BC municipalities.

[TransitLbl] [Start Condition]The closest Skytrain/Railway station is [SkytrainStationName] on [SkytrainStationLine] line in [SkytrainDistance] km away. [End Condition]The closest Bus station is [BusStationName] in [BusDistance] km away.

[EducationLbl] [TotalPeopleWithDegree]% of people with university certificate/degree live in [DemographicsArea] which is [DegreeStatus] in comparison with the Lower Mainland average. [TotalPeopleWithoutDegree]% of population in [DemographicsArea] have no certificate or degree. This number is [NoDegreeStatus] compared to the average in Metro Vancouver and Fraser Valley.

[IncomeLbl] Median Household Income near this dwelling in [DemographicsArea] is [MedianHouseHoldIncome] which is [MedianIncomeStatus] in comparison with the Lower Mainland average.

[UnemploymentLbl] Unemployment rate in the area around Listing # [MLS] is [UnemploymentRate] which is [UnemploymentStatus] in comparison to the other Lower Mainland neighbourhoods.

[DaycaresLbl] The closest daycare without violation is [ClosestDaycareNoViolations]. And it is in [ClosestDaycareNoViolationsDistance]km away.

[ClimateLbl] The closest weather station is [ClosestWeatherStation]. It is located [ClosestWeatherStationDistance]km away. Weather station elevation is [ClosestWeatherStationElevation]m.  

Daily average temperature around this station is [ClosestWeatherStationTemperature]C which is [ClosestWeatherStationTemperatureStatus] compared to the  local average. 

Rainfall is about [ClosestWeatherStationRainfallRate]mm yearly which is [ClosestWeatherStationRainfallStatus] compared to the local averages.

Snowfall is [ClosesWeatherStationSnowfallRate]mm yearly. This number is [ClosesWeatherStationSnowFallStatus] compared to other local neighbourhoods.

The closest bus station is [BusStopDistance] km away.

[FloodAreaLbl]  According to an official flood area map [Street] home for sale [FloodStatus] . This information is for general informational purposes only. You should not use such information in determining the chances of this house being flooded. 

[SkytrainNoiseLbl] This listing is [SkytrainNoise].

[RoadNoiseLbl] This home [RoadNoise].

[AirportNoiseLbl] This area [AirportNoise].

[PowerLinesLbl] It is [PowerLines].

[CemeteriesLbl] It is [Cemeteries].

[ALRLbl] It [ALRStatus].

[PopulationChangeLbl] This real estate is located in [Subdivision] where population change between 2006 and 2011 was [PopulationChange]. This is [PopulationChangeStatus] in comparison to average growth rate of this region.

[AverageChildrenLbl]  Average Number of Children in [Subdivision] is [ChildrenRate]. This is [ChildrenStatus] number in comparison to the whole region.

[MedianAgeLbl]  [MedianAgeRate]% of population in this area is 65 and over. This percentage is [MedianAgeStatus] in comparison to other BC cities.

[PopulationDensityLbl]  Population Density is [PopulationDensityStatus]. [PopulationDensityRate] people per sq.km. 

[AverageOwnerPaymentsLbl]  Average Owner payments are [AverageOwnerPaymentsStatus] in this area. $[AverageOwnerPaymentsCount] owners spent in average for the dwelling.

[TransportLbl]  [PublicTransportRate]% of all population in the area around this real estate using Public Transport and only [WalkingBicycleRate]% walking and using bicycle.";

        return $seoDescriptionNotParsed;
    }
}