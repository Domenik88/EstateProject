<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 18.09.2020
 *
 * @package estateblock20
 */

namespace App\Service\Listing;


use App\Entity\Listing;
use App\Entity\School;
use App\Repository\ListingRepository;
use App\Repository\SchoolRepository;
use App\Service\Geo\HereRouteService;
use App\Service\Geo\Point;
use App\Service\School\SchoolData;
use DateTime;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use function MongoDB\Driver\Monitoring\removeSubscriber;

class ListingService
{
    private EntityManagerInterface $entityManager;
    private ListingRepository $listingRepository;
    private ListingMediaService $listingMediaService;
    private ListingListSinglePageListingsCoordinates $listingListSinglePageListingsCoordinates;
    private LoggerInterface $logger;
    private ListingSearchDataService $listingSearchDataService;
    private SchoolRepository $schoolRepository;
    private HereRouteService $hereRouteService;

    public function __construct(EntityManagerInterface $entityManager, ListingRepository $listingRepository, ListingMediaService $listingMediaService, ListingListSinglePageListingsCoordinates $listingListSinglePageListingsCoordinates, LoggerInterface $logger, ListingSearchDataService $listingSearchDataService, SchoolRepository $schoolRepository, HereRouteService $hereRouteService)
    {
        $this->entityManager = $entityManager;
        $this->listingRepository = $listingRepository;
        $this->listingMediaService = $listingMediaService;
        $this->listingListSinglePageListingsCoordinates = $listingListSinglePageListingsCoordinates;
        $this->logger = $logger;
        $this->listingSearchDataService = $listingSearchDataService;
        $this->schoolRepository = $schoolRepository;
        $this->hereRouteService = $hereRouteService;
    }

    public function createFromDdfResult(array $result): Listing
    {
        $listing = new Listing();
        $listing->setFeedID('ddf');
        $listing->setCity($result['City']);
        $listing->setFeedListingID($result['ListingKey']);
        $listing->setListPrice($result['ListPrice']);
        $listing->setOriginalPrice($result['ListPrice']);
        $listing->setMlsNum($result['ListingId']);
        $listing->setPhotosCount($result['PhotosCount']);
        $listing->setPostalCode($result['PostalCode']);
        $listing->setUnparsedAddress($result['UnparsedAddress']);
        $listing->setStateOrProvince($result['StateOrProvince']);
        $listing->setCountry($result['Country']);
        $listing->setStatus(ListingConstants::NEW_LISTING_STATUS);
        $listing->setProcessingStatus(ListingConstants::NONE_PROCESSING_LISTING_STATUS);
        $listing->setLastUpdateFromFeed(new \DateTime());
        $listing->setRawData($result);
        if ( $result['Latitude'] != '' or $result['Longitude'] != '' ) {
            $listing->setCoordinates(new Point($result['Latitude'], $result['Longitude']));
        }
        $listing->setType($result['PropertyType']);
        $listing->setOwnershipType($result['OwnershipType']);
        $listing->setBedrooms($result['BedroomsTotal'] ? (int)$result['BedroomsTotal'] : null);
        $listing->setLivingArea($result['BuildingAreaTotal'] ? (int)$result['BuildingAreaTotal'] : 0);
        $listing->setLotSize($result['LotSizeArea'] ? (int)$result['LotSizeArea'] : 0);
        $listing->setYearBuilt($result['YearBuilt'] ? (int)$result['YearBuilt'] : null);
        $listing->setContractDate($result['ListingContractDate'] ? new \DateTime($result['ListingContractDate']) : null);
        $listing->setSubdivision($result['SubdivisionName'] ? $result['SubdivisionName'] : null);

        $this->entityManager->persist($listing);

        $this->entityManager->flush();

        return $listing;
    }

    public function upsertFromDdfResult(array $result, bool $updateStatuses = true): Listing
    {
        $existingListing = $this->listingRepository->findOneBy([
            'feedID' => 'ddf',
            'feedListingID' => $result['ListingKey'],
            'deletedDate' => null,
        ]);
        if ( !$existingListing ) {
            return $this->createFromDdfResult($result);
        }
        $existingListing->setFeedID('ddf');
        $existingListing->setMlsNum($result['ListingId']);
        $existingListing->setCity($result['City']);
        $existingListing->setListPrice($result['ListPrice']);
        $existingListing->setPhotosCount($result['PhotosCount']);
        $existingListing->setPostalCode($result['PostalCode']);
        $existingListing->setUnparsedAddress($result['UnparsedAddress']);
        $existingListing->setStateOrProvince($result['StateOrProvince']);
        $existingListing->setCountry($result['Country']);
        if ( $updateStatuses ) {
            if ( $existingListing->getStatus() != 'new' ) {
                $existingListing->setStatus(ListingConstants::UPDATED_LISTING_STATUS);
            }
            $existingListing->setProcessingStatus(ListingConstants::NONE_PROCESSING_LISTING_STATUS);
        }
        $existingListing->setLastUpdateFromFeed(new \DateTime());
        $existingListing->setRawData($result);
        if ( $result['Latitude'] != '' or $result['Longitude'] != '' ) {
            $existingListing->setCoordinates(new Point($result['Latitude'], $result['Longitude']));
        }
        $existingListing->setType($result['PropertyType']);
        $existingListing->setOwnershipType($result['OwnershipType']);
        $existingListing->setBedrooms($result['BedroomsTotal'] ? (int)$result['BedroomsTotal'] : null);
        $existingListing->setLivingArea($result['BuildingAreaTotal'] ? (int)$result['BuildingAreaTotal'] : 0);
        $existingListing->setLotSize($result['LotSizeArea'] ? (int)$result['LotSizeArea'] : 0);
        $existingListing->setYearBuilt($result['YearBuilt'] ? (int)$result['YearBuilt'] : null);
        $existingListing->setContractDate($result['ListingContractDate'] ? new \DateTime($result['ListingContractDate']) : null);
        $existingListing->setSubdivision($result['SubdivisionName'] ? $result['SubdivisionName'] : null);

        $this->entityManager->flush();

        return $existingListing;
    }

    public function getAdminListingList(array $criteria, int $currentPage = 1, int $limit = 50, int $offset = 0): ListingListSearchResult
    {
        $results = $this->listingRepository->findBy(
            $criteria,
            ['feedListingID' => 'DESC'],
            $limit,
            $offset);
        $listingListCount = $this->getAdminListingListCount();
        $listingList = [];
        foreach ( $results as $result ) {
            $listingList[] = $this->listingSearchDataService->constructSearchListingData($result);
        }
        $pageCounter = 1;
        if (count($listingList) != 1)
        {
            $pageCounter = ceil($listingListCount / $limit);
        }

        return new ListingListSearchResult($listingListCount, $listingList, $currentPage, $pageCounter);
    }

    public function getAdminListingListCount()
    {
        return $this->listingRepository->count([
            'deletedDate' => null,
        ]);
    }

    public function setAdminListingSelfListing(string $mlsId)
    {
        $singleListing = $this->listingRepository->findOneBy([
            'id' => $mlsId
        ]);
        $selfListingStatus = $singleListing->getSelfListing() ? false : true;
        $singleListing->setSelfListing($selfListingStatus);

        $this->entityManager->flush();

        return true;
    }

    public function getListingList(string $feedName, int $currentPage, int $limit = 50, int $offset = 0): ?ListingListSearchResult
    {
        $results = $this->listingRepository->findBy([
            'feedID' => $feedName,
            'status' => [ ListingConstants::LIVE_LISTING_STATUS, ListingConstants::UPDATED_LISTING_STATUS ],
            'deletedDate' => null,
        ],
            null,
            $limit,
            $offset);
        $listingListCount = $this->getListingListCount($feedName);
        $pageCounter = ceil($listingListCount / $limit);

        return new ListingListSearchResult($listingListCount, $results, $currentPage, $pageCounter);
    }

    public function getSingleListing(string $province, string $mlsNum, string $feedName): ?Listing
    {
        return $this->listingRepository->findOneBy([
            'stateOrProvince' => $province,
            'mlsNum' => $mlsNum,
            'feedID' => $feedName,
            'deletedDate' => null,
            'status' => [ ListingConstants::LIVE_LISTING_STATUS, ListingConstants::UPDATED_LISTING_STATUS ],
        ]);
    }

    public function getListingListCount(string $feedName)
    {
        return $this->listingRepository->count([
            'feedID' => $feedName,
            'deletedDate' => null,
            'status' => [ ListingConstants::LIVE_LISTING_STATUS, ListingConstants::UPDATED_LISTING_STATUS ],
        ]);
    }

    public function getBatchListingsForProcessing(string $feedName, int $batchSize): array
    {
        return $this->listingRepository->findBy([
            'feedID' => $feedName,
            'deletedDate' => null,
            'status' => [ ListingConstants::NEW_LISTING_STATUS, ListingConstants::UPDATED_LISTING_STATUS ],
            'processingStatus' => ListingConstants::NONE_PROCESSING_LISTING_STATUS,
        ], [ 'lastUpdateFromFeed' => 'ASC' ], $batchSize);
    }

    public function setListingStatus(Listing $listing, string $status)
    {
        $listing->setStatus($status);

        $this->entityManager->flush();
    }

    public function setListingProcessingStatus($listingId, string $status)
    {
        $listing = $this->listingRepository->findOneBy([
            'id' => $listingId
        ]);
        $listing->setProcessingStatus($status);

        $this->entityManager->flush();
    }

    public function setBatchProcessingStatus(array $batch, string $status)
    {
        try {
            $this->entityManager->getConnection()->beginTransaction();
            foreach ( $batch as $batchItem ) {
                $batchItem->setProcessingStatus($status);
            }
            $this->entityManager->flush();
            $this->entityManager->getConnection()->commit();
        } catch ( Exception $e ) {
            $this->entityManager->getConnection()->rollBack();
            $this->logger->error($e->getMessage());
            throw $e;
        }
    }

    public function setListingPhotosNamesObject(Listing $listing, array $photoNamesArray): Listing
    {
        $existingListing = $this->listingRepository->findOneBy([
            'feedID' => 'ddf',
            'feedListingID' => $listing->getFeedListingID(),
            'deletedDate' => null,
        ]);
        $existingListing->setImagesData($photoNamesArray);

        $this->entityManager->flush();

        return $existingListing;
    }

    public function setListingCoordinates(Listing $listing, Point $point): Listing
    {
        $existingListing = $this->listingRepository->findOneBy([
            'mlsNum' => $listing->getMlsNum(),
            'feedListingID' => $listing->getFeedListingID(),
            'deletedDate' => null,
        ]);
        $existingListing->setCoordinates($point);

        $this->entityManager->flush();

        return $existingListing;
    }

    public function getListingData(string $province, string $mlsNum, string $feedName): ?object
    {
        $singleListing = $this->getSingleListing($province, $mlsNum, $feedName);
        if (is_null($singleListing)) {
            return null;
        }

        return $this->listingSearchDataService->constructSearchListingData($singleListing);
    }

    public function getListingListCoordinates(string $feedName, int $currentPage, int $limit = 50, int $offset = 0): array
    {
        $results = $this->listingRepository->findBy([
            'feedID' => $feedName,
            'status' => [ ListingConstants::LIVE_LISTING_STATUS, ListingConstants::UPDATED_LISTING_STATUS ],
            'deletedDate' => null,
        ],
            null,
            $limit,
            $offset);
        return $this->listingListSinglePageListingsCoordinates->getListingListCoordinates($results);
    }

    public function getAllActiveListingsForMapBox(float $neLat, float $neLng, float $swLat, float $swLng): array
    {
        return $this->listingRepository->getAllListingsInMapBox($neLat, $neLng, $swLat, $swLng);
    }

    public function getSelfListingsForHomepage(): array
    {
        $results = $this->listingRepository->findBy([
            'selfListing' => true,
            'status' => [ ListingConstants::LIVE_LISTING_STATUS, ListingConstants::UPDATED_LISTING_STATUS ],
            'deletedDate' => null,
        ],
        ['lastUpdateFromFeed' => 'DESC']);
        $listingList = [];
        foreach ( $results as $result ) {
            $listingList[] = $this->listingSearchDataService->constructSearchListingData($result);
        }

        return $listingList;
    }

    public function getCitiesCounters(): ?array
    {
        $cityes = ['Vancouver','North Vancouver','Burnaby','Coquitlam','Surrey','Richmond','West Vancouver','Langley','Maple Ridge','Pitt Meadows','Mission','Abbotsford','Chilliwack','Pitt Meadows','Mission'];
        $result = $this->listingRepository->getCounters($cityes, 'British Columbia', 'ddf');
        $cityCounters = [];
        foreach ( $result as $item ) {
            $cityCounters[] = new CitiesCounterResult($item['city'],$item['counter'], 'ddf', 'British Columbia', 'Vancouver,BC');
        }

        return $cityCounters;
    }

    public function getFeaturedProperties()
    {
        $results = $this->listingRepository->getListingsByCriteria(new ListingCriteria('ddf',[ ListingConstants::LIVE_LISTING_STATUS, ListingConstants::UPDATED_LISTING_STATUS ]));
        $featuredProperties = [];
        foreach ( $results as $result ) {
            $featuredProperties[] = $this->listingSearchDataService->constructSearchListingData($result);
        }

        return $featuredProperties;
    }

    public function getSearchFormObject(): ?array
    {
        return [
            'type' => $this->getPropertyTypes(),
            'priceFrom' => $this->getPropertyPriceFrom(),
            'priceTo' => $this->getPropertyPriceTo(),
            'beds' => $this->getPropertyBedrooms(),
            'baths' => $this->getPropertyBathrooms(),
        ];
    }

    public function setListingSchools(Listing $listing)
    {
        $coordinates = $listing->getCoordinates();
        $publicSchools = $this->schoolRepository->getPublicSchools($coordinates);
        $schoolObject = [];
        foreach ( $publicSchools as $publicSchool ) {
            $distance = $this->getSchoolDistance($listing,$publicSchool);
            $schoolObject['public'][] = new SchoolData($publicSchool, $distance);
        }
        $privateSchools = $this->schoolRepository->getPrivateSchools($coordinates);
        foreach ( $privateSchools['elementary'] as $privateSchoolElementary ) {
            $distance = $this->getSchoolDistance($listing,$privateSchoolElementary);
            $schoolObject['private']['elementary'] = new SchoolData($privateSchoolElementary, $distance);
        }
        foreach ( $privateSchools['secondary'] as $privateSchoolSecondary ) {
            $distance = $this->getSchoolDistance($listing,$privateSchoolSecondary);
            $schoolObject['private']['secondary'] = new SchoolData($privateSchoolSecondary, $distance);
        }

        return $listing->setSchoolsData($schoolObject);
    }

    public function getSchoolDistance(Listing $listing, School $school): ?string
    {
        try {
            $request = $this->hereRouteService->getRoute($listing->getCoordinates(), $school->getCoordinates());
            $routes = json_decode($request->getBody()->getContents())->response->route;
            $distance = [];
            foreach ($routes as $route) {
                $distance[] = $route->summary->distance;
            }
            return round(min($distance) / 1000, 2) . ' km';
        } catch (\Exception $e) {
            return null;
        }
    }

    private function getPropertyTypes(): ?object
    {
        return (object)[
            'Aaprtment/Condo',
            'House',
            'Multifamily',
            'Townhouse',
            'Land Only',
            'Other',
        ];
    }

    private function getPropertyPriceFrom(): ?object
    {
        return (object)[
            'No min',
            10000,
            20000,
            30000,
            50000,
            100000,
            130000,
            150000,
            200000,
            250000,
            300000,
            350000,
            400000,
            450000,
            500000,
            550000,
            600000,
            650000,
            700000,
            750000,
            800000,
            850000,
            900000,
            950000,
            1000000,
            1100000,
            1200000,
            1250000,
            1400000,
            1500000,
            1600000,
            1700000,
            1750000,
            1800000,
            1900000,
            2000000,
            2250000,
            2500000,
            2750000,
            3000000,
            3500000,
            4000000,
            5000000,
            10000000,
            20000000,
        ];
    }

    private function getPropertyPriceTo(): ?object
    {
        return (object)[
            'No max',
            10000,
            20000,
            30000,
            50000,
            100000,
            130000,
            150000,
            200000,
            250000,
            300000,
            350000,
            400000,
            450000,
            500000,
            550000,
            600000,
            650000,
            700000,
            750000,
            800000,
            850000,
            900000,
            950000,
            1000000,
            1100000,
            1200000,
            1250000,
            1400000,
            1500000,
            1600000,
            1700000,
            1750000,
            1800000,
            1900000,
            2000000,
            2250000,
            2500000,
            2750000,
            3000000,
            3500000,
            4000000,
            5000000,
            10000000,
            20000000,
        ];
    }

    private function getPropertyBathrooms(): ?object
    {
        return (object)[
            'Any',
            '1+',
            '2+',
            '3+',
            '4+',
            '5+',
        ];
    }

    private function getPropertyBedrooms(): ?object
    {
        return (object)[
            'Studio+',
            '1+',
            '2+',
            '3+',
            '4+',
        ];
    }

}