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
    private ListingSearchDataService $listingSearchDataService;

    public function __construct(EntityManagerInterface $entityManager, ListingRepository $listingRepository, ListingMediaService $listingMediaService, ListingListSinglePageListingsCoordinates $listingListSinglePageListingsCoordinates, LoggerInterface $logger, ListingSearchDataService $listingSearchDataService)
    {
        $this->entityManager = $entityManager;
        $this->listingRepository = $listingRepository;
        $this->listingMediaService = $listingMediaService;
        $this->listingListSinglePageListingsCoordinates = $listingListSinglePageListingsCoordinates;
        $this->logger = $logger;
        $this->listingSearchDataService = $listingSearchDataService;
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
            'feedListingID' => $mlsId
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
            $cityCounters[] = new CitiesCounterResult($item['city'],$item['counter'], 'ddf', 'British Columbia');
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

}