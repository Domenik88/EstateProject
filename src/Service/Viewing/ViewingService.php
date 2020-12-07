<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 04.12.2020
 *
 * @package estateblock20
 */

namespace App\Service\Viewing;

use App\Entity\Listing;
use App\Entity\User;
use App\Entity\Viewing;
use App\Repository\ListingRepository;
use App\Repository\UserRepository;
use App\Repository\ViewingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ViewingService
{
    private EntityManagerInterface $entityManager;
    private ViewingRepository $viewingRepository;
    private ListingRepository $listingRepository;
    private UserRepository $userRepository;
    private UserPasswordEncoderInterface $encoder;

    public function __construct(EntityManagerInterface $entityManager, ViewingRepository $viewingRepository, UserRepository $userRepository, ListingRepository $listingRepository, UserPasswordEncoderInterface $encoder)
    {
        $this->entityManager = $entityManager;
        $this->viewingRepository = $viewingRepository;
        $this->userRepository = $userRepository;
        $this->listingRepository = $listingRepository;
        $this->encoder = $encoder;
    }

    public function createViewing(string $request): ViewingResponseStatusCode
    {
        try {
            $formData = json_decode($request);
            $user = $this->getUser($formData);
            $listing = $this->getListing($formData->listingId->value);
            if ( !$listing ) {
                return new ViewingResponseStatusCode(404, 'Listing not found');
            }
            $viewing = new Viewing();
            $viewing->setUser($user);
            $viewing->setListing($listing);
            $viewing->setStatus('new');
            $this->entityManager->persist($viewing);
            $this->entityManager->flush();
            return new ViewingResponseStatusCode(201, 'Viewing created');
        } catch (\Exception $e) {
            return new ViewingResponseStatusCode(500, $e->getMessage());
        }
    }

    private function getUser($userData)
    {
        $user = $this->userRepository->findOneBy([
            'email' => $userData->email->value
        ]);
        if ( !$user ) {
            return $this->createUserFromData($userData);
        }
        return $user;
    }

    private function createUserFromData($formData): User
    {
        $user = new User();
        $user->setEmail($formData->email->value);
        $user->setName($formData->uname->value);
        $user->setPhoneNumber($formData->phone->value);
        $user->setRoles(["ROLE_USER"]);
        $user->setPassword($this->encoder->encodePassword($user, $formData->phone->value));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    private function getListing($listingId): ?Listing
    {
        $listing = $this->listingRepository->findOneBy([
            'feedListingID' => $listingId
        ]);
        if ( !$listing ) {
            return null;
        }
        return $listing;
    }

}