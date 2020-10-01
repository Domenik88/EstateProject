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
use Doctrine\ORM\EntityManagerInterface;

class ListingService
{
    private EntityManagerInterface $entityManager;
    private ListingRepository $listingRepository;
    private ListingMediaService $listingMediaService;

    public function __construct(EntityManagerInterface $entityManager, ListingRepository $listingRepository, ListingMediaService $listingMediaService)
    {
        $this->entityManager = $entityManager;
        $this->listingRepository = $listingRepository;
        $this->listingMediaService = $listingMediaService;
    }

    public function createFromDdfResult(array $result)
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
        $listing->setStatus(ListingConstants::NEW_LISTING_STATUS);
        $listing->setProcessingStatus(ListingConstants::NONE_PROCESSING_LISTING_STATUS);
        $listing->setLastUpdateFromFeed(new \DateTime());

        $this->entityManager->persist($listing);

        $this->entityManager->flush();

        return $listing;
    }

    public function upsertFromDdfResult(array $result)
    {
        $existingListing = $this->listingRepository->findOneBy([
            'mlsNum' => $result['ListingId'],
            'feedListingID' => $result['ListingKey']
        ]);
        if (!$existingListing) {
            return $this->createFromDdfResult($result);
        }
        $existingListing->setFeedID('ddf');
        $existingListing->setCity($result['City']);
        $existingListing->setListPrice($result['ListPrice']);
        $existingListing->setPhotosCount($result['PhotosCount']);
        $existingListing->setPostalCode($result['PostalCode']);
        $existingListing->setUnparsedAddress($result['UnparsedAddress']);
        $existingListing->setStatus(ListingConstants::UPDATED_LISTING_STATUS);
        $existingListing->setProcessingStatus(ListingConstants::NONE_PROCESSING_LISTING_STATUS);
        $existingListing->setLastUpdateFromFeed(new \DateTime());

        $this->entityManager->flush();
    }

    public function getListingList(string $feedName, int $currentPage, int $limit = 50, int $offset = 0)
    {
        $results = $this->listingRepository->findBy([
            'feedID' => $feedName,
            'status' => [ListingConstants::LIVE_LISTING_STATUS,ListingConstants::UPDATED_LISTING_STATUS],
        ],
        null,
        $limit,
        $offset );
        $listingListCount = $this->getListingListCount($feedName);
        $pageCounter = ceil($listingListCount / $limit);

        return new ListingListSearchResult($listingListCount,$results, $currentPage, $pageCounter);
    }

    public function getSingleListing(string $mlsNum, string $feedName): Listing
    {
        return $this->listingRepository->findOneBy([
            'mlsNum' => $mlsNum,
            'feedID' => $feedName
        ]);
    }

    public function getListingListCount(string $feedName)
    {
        return $this->listingRepository->count([
            'feedID' => $feedName,
            'status' => [ListingConstants::LIVE_LISTING_STATUS,ListingConstants::UPDATED_LISTING_STATUS],
        ]);
    }

    public function getSingleListingForProcessing(string $feedName): Listing
    {
        return $this->listingRepository->findOneBy([
            'feedID' => $feedName,
            'status' => [ListingConstants::NEW_LISTING_STATUS,ListingConstants::UPDATED_LISTING_STATUS],
            'processingStatus' => ListingConstants::NONE_PROCESSING_LISTING_STATUS,
        ],['lastUpdateFromFeed'=>'ASC']);
    }

    public function setListingStatus(Listing $listing, string $status)
    {
        $existingListing = $this->listingRepository->findOneBy([
            'mlsNum' => $listing->getMlsNum(),
            'feedListingID' => $listing->getFeedListingID(),
        ]);
        $existingListing->setStatus($status);

        $this->entityManager->flush();
    }

    public function setListingProcessingStatus(Listing $listing, string $status)
    {
        $existingListing = $this->listingRepository->findOneBy([
            'mlsNum' => $listing->getMlsNum(),
            'feedListingID' => $listing->getFeedListingID(),
        ]);
        $existingListing->setProcessingStatus($status);

        $this->entityManager->flush();
    }

    public function setListingPhotosNamesObject(Listing $listing, array $photoNamesArray)
    {
        $photoNamesObject = (object)$photoNamesArray;
        $existingListing = $this->listingRepository->findOneBy([
            'mlsNum' => $listing->getMlsNum(),
            'feedListingID' => $listing->getFeedListingID(),
        ]);
        $existingListing->setImagesData($photoNamesObject);

        $this->entityManager->flush();
    }

    public function setListingCoordinates(Listing $listing, Point $point)
    {
        $existingListing = $this->listingRepository->findOneBy([
            'mlsNum' => $listing->getMlsNum(),
            'feedListingID' => $listing->getFeedListingID(),
        ]);
        $existingListing->setCoordinates($point);

        $this->entityManager->flush();
    }

    public function getListingData(string $mlsNum, string $feedName): array
    {
        $singleListing = $this->getSingleListing($mlsNum, $feedName);
        $listingImagesUrlArray = $this->listingMediaService->getListingPhotos($singleListing);

        return ['listing'=>$singleListing,'photos'=>$listingImagesUrlArray];
    }

}