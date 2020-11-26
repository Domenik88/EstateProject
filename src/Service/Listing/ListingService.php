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
use App\Repository\ListingRepository;
use App\Service\Geo\Point;
use DateTime;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class ListingService
{
    private EntityManagerInterface $entityManager;
    private ListingRepository $listingRepository;
    private ListingMediaService $listingMediaService;
    private ListingListSinglePageListingsCoordinates $listingListSinglePageListingsCoordinates;
    private LoggerInterface $logger;

    public function __construct(EntityManagerInterface $entityManager, ListingRepository $listingRepository, ListingMediaService $listingMediaService, ListingListSinglePageListingsCoordinates $listingListSinglePageListingsCoordinates, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->listingRepository = $listingRepository;
        $this->listingMediaService = $listingMediaService;
        $this->listingListSinglePageListingsCoordinates = $listingListSinglePageListingsCoordinates;
        $this->logger = $logger;
    }

    public function createFromDdfResult(array $result): Listing
    {
        $listing = new Listing();
        $listing->setFeedID('ddf');
        $listing->setCity($result['City']);
        $listing->setFeedListingID($result['ListingKey']);
        $listing->setListPrice($result['ListPrice']);
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

        $this->entityManager->persist($listing);

        $this->entityManager->flush();

        return $listing;
    }

    public function upsertFromDdfResult(array $result, bool $updateStatuses = true): Listing
    {
        $existingListing = $this->listingRepository->findOneBy([
            'feedID' => 'ddf',
            'feedListingID' => $result['ListingKey']
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

        $this->entityManager->flush();

        return $existingListing;
    }

    public function getListingList(string $feedName, int $currentPage, int $limit = 50, int $offset = 0)
    {
        $results = $this->listingRepository->findBy([
            'feedID' => $feedName,
            'status' => [ ListingConstants::LIVE_LISTING_STATUS, ListingConstants::UPDATED_LISTING_STATUS ],
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
            'status' => [ ListingConstants::LIVE_LISTING_STATUS, ListingConstants::UPDATED_LISTING_STATUS ],
        ]);
    }

    public function getListingListCount(string $feedName)
    {
        return $this->listingRepository->count([
            'feedID' => $feedName,
            'status' => [ ListingConstants::LIVE_LISTING_STATUS, ListingConstants::UPDATED_LISTING_STATUS ],
        ]);
    }

    public function getBatchListingsForProcessing(string $feedName, int $batchSize): array
    {
        return $this->listingRepository->findBy([
            'feedID' => $feedName,
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
        ]);
        $existingListing->setImagesData($photoNamesArray);

        $this->entityManager->flush();

        return $existingListing;
    }

    public function setListingCoordinates(Listing $listing, Point $point)
    {
        $existingListing = $this->listingRepository->findOneBy([
            'mlsNum' => $listing->getMlsNum(),
            'feedListingID' => $listing->getFeedListingID(),
        ]);
        $existingListing->setCoordinates($point);

        $this->entityManager->flush();

        return $existingListing;
    }

    public function getListingData(string $province, string $mlsNum, string $feedName): ?array
    {
        $singleListing = $this->getSingleListing($province, $mlsNum, $feedName);
        if (is_null($singleListing)) {
            return [ 'listing' => null ];
        }
        $listingImagesUrlArray = (object)$this->listingMediaService->getListingPhotos($singleListing);

        $listingCoordinates = (object)[
            'lat' => $singleListing->getCoordinates()->getLatitude(),
            'lng' => $singleListing->getCoordinates()->getLongitude(),
        ];

        $listingAddress = (object)[
            'country' => $singleListing->getCountry(),
            'state' => $singleListing->getStateOrProvince(),
            'city' => $singleListing->getCity(),
            'postalCode' => $singleListing->getPostalCode(),
            'streetAddress' => $singleListing->getUnparsedAddress(),
        ];

        $listingMetrics = (object)[
            'bedRooms' => $singleListing->getRawData()['BedroomsTotal'],
            'bathRooms' => $singleListing->getRawData()['BathroomsTotal'],
            'stories' => $singleListing->getRawData()['Stories'],
            'lotSize' => $this->getListingLotSize($singleListing),
            'sqrtFootage' => $this->getListingBuildingAreaTotal($singleListing),
        ];

        $listingFinancials = (object)[
            'listingPrice' => $singleListing->getListPrice(),
            'strataMaintenanceFee' => null,
            'grossTaxes' => null,
            'grossTaxYear' => null,
            'originalListingPrice' => $singleListing->getListPrice(),
        ];

        $listingAgent = (object)[
            'agentFullName' => $singleListing->getRawData()['ListAgentFullName'],
            'agencyName' => $singleListing->getRawData()['ListOfficeName'],
            'agentPhone' => $singleListing->getRawData()['ListAgentOfficePhone'],
            'agentEmail' => $singleListing->getRawData()['ListAgentEmail'],
        ];

        $listingObject = (object)[
            'yearBuilt' => $singleListing->getRawData()['YearBuilt'],
            'mlsNumber' => $singleListing->getMlsNum(),
            'feedId' => $singleListing->getFeedID(),
            'type' => $singleListing->getRawData()['PropertyType'],
            'ownershipType' => $singleListing->getRawData()['OwnershipType'],
            'images' => $listingImagesUrlArray,
            'coordinates' => $listingCoordinates,
            'daysOnTheMarket' => $this->getListingDaysOnTheMarket($singleListing->getRawData()['ListingContractDate']),
            'description' => $singleListing->getRawData()['PublicRemarks'],
            'address' => $listingAddress,
            'metrics' => $listingMetrics,
            'financials' => $listingFinancials,
            'listingAgent' => $listingAgent,
        ];

        return [ 'listing' => $singleListing, 'photos' => $listingImagesUrlArray, 'listingObject' => $listingObject ];
    }

    public function getListingListCoordinates(string $feedName, int $currentPage, int $limit = 50, int $offset = 0): array
    {
        $results = $this->listingRepository->findBy([
            'feedID' => $feedName,
            'status' => [ ListingConstants::LIVE_LISTING_STATUS, ListingConstants::UPDATED_LISTING_STATUS ],
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

    public function getListingDaysOnTheMarket($listingContractDate)
    {
        return date_diff(new DateTime(), new DateTime($listingContractDate))->days;
    }

    public function getListingLotSize(Listing $listing): ?string
    {
        if (!is_null($listing->getRawData()['LotSizeArea']) || $listing->getRawData()['LotSizeArea'] != 0) {
            return $listing->getRawData()['LotSizeArea'] . $listing->getRawData()['LotSizeUnits'];
        }

        return null;
    }

    public function getListingBuildingAreaTotal(Listing $listing): ?string
    {
        if (!is_null($listing->getRawData()['BuildingAreaTotal']) || $listing->getRawData()['BuildingAreaTotal'] != 0) {
            return $listing->getRawData()['BuildingAreaTotal'] . $listing->getRawData()['BuildingAreaUnits'];
        }

        return null;
    }
}