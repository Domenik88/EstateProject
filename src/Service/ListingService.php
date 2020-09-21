<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 18.09.2020
 *
 * @package estateblock20
 */

namespace App\Service;


use App\Entity\Listing;
use App\Repository\ListingRepository;
use Doctrine\ORM\EntityManagerInterface;

class ListingService
{
    private EntityManagerInterface $entityManager;
    private ListingRepository $listingRepository;

    public function __construct(EntityManagerInterface $entityManager, ListingRepository $listingRepository)
    {
        $this->entityManager = $entityManager;
        $this->listingRepository = $listingRepository;
    }

    public function createFromDdfResult(array $result)
    {
        $listing = new Listing();
        $listing->setFeedID('Ddf');
        $listing->setCity($result['City']);
        $listing->setFeedListingID($result['ListingKey']);
        $listing->setListPrice($result['ListPrice']);
        $listing->setMlsNum($result['ListingId']);
        $listing->setPhotosCount($result['PhotosCount']);
        $listing->setPostalCode($result['PostalCode']);
        $listing->setUnparsedAddress($result['UnparsedAddress']);

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
        $existingListing->setFeedID('Ddf');
        $existingListing->setCity($result['City']);
        $existingListing->setListPrice($result['ListPrice']);
        $existingListing->setPhotosCount($result['PhotosCount']);
        $existingListing->setPostalCode($result['PostalCode']);
        $existingListing->setUnparsedAddress($result['UnparsedAddress']);

        $this->entityManager->flush();
    }
}