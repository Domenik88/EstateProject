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
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
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

    public function createViewing(ViewingRequestData $requestData): ViewingResponseStatusCode
    {
        try {
            $user = $this->getOrCreateUser($requestData);
            $listing = $this->getListing($requestData->getListingId());
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
        } catch (UniqueConstraintViolationException $e) {
            return new ViewingResponseStatusCode(409, 'Viewing record already exists');
        } catch (\Exception $e) {
            return new ViewingResponseStatusCode(500, $e->getMessage());
        }
    }

    private function getOrCreateUser(ViewingRequestData $userData): User
    {
        $user = $this->userRepository->findOneBy([
            'email' => $userData->getEmail(),
        ]);
        if ( !$user ) {
            return $this->createUserFromData($userData);
        }
        return $user;
    }

    private function createUserFromData(ViewingRequestData $formData): User
    {
        $user = new User();
        $user->setEmail($formData->getEmail());
        $user->setName($formData->getName());
        $user->setPhoneNumber($formData->getPhone());
        $user->setRoles(["ROLE_USER"]);
        $user->setPassword($this->encoder->encodePassword($user, $formData->getPhone()));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    private function getListing($listingId): ?Listing
    {
        $listing = $this->listingRepository->findOneBy([
            'id' => $listingId
        ]);
        if ( !$listing ) {
            return null;
        }
        return $listing;
    }

}