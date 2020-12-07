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
use Symfony\Component\HttpFoundation\Request;
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

    public function createViewing(Request $request): ?Viewing
    {
        $formData = json_decode($request->request->get('data'));
        $user = $this->getUser($formData);
        $listing = $this->getListing($formData);

        $viewing = new Viewing();
        $viewing->setUser($user);
        $viewing->setListing($listing);
        $viewing->setStatus('new');

        $this->entityManager->persist($viewing);
        $this->entityManager->flush();

        return $viewing;
    }

    private function getUser($userData)
    {
        $user = $this->userRepository->findOneBy([
            'email' => $userData->email
        ]);
        if ( !$user ) {
            return $this->createUserFromData($userData);
        }
        return $user;
    }

    private function createUserFromData($formData): User
    {
        $user = new User();
        $user->setEmail($formData->email);
        $user->setName($formData->name);
        $user->setPhoneNumber($formData->phone);
        $user->setRoles(["ROLE_USER"]);
        $user->setPassword($this->encoder->encodePassword($user, $formData->phone));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    private function getListing($formData): Listing
    {
        $listing = $this->listingRepository->findOneBy([
            'feedListingID' => $formData->listingId
        ]);
        if ( !$listing ) {
            throw new \Exception("Listing with id: $formData->listingId not found!");
        }
        return $listing;
    }
}