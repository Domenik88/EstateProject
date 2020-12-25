<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 24.12.2020
 *
 * @package estateblock20
 */

namespace App\Service\User;

use App\Repository\ListingRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserService
{
    private UserRepository $userRepository;
    private ListingRepository $listingRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(UserRepository $userRepository, ListingRepository $listingRepository, EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->listingRepository = $listingRepository;
        $this->entityManager = $entityManager;
    }

    public function toggleFavoriteListing(string $listingId, int $userId)
    {
        // TODO: add listing in favorites to user and add user identificator here and logic in twig side
        $user = $this->userRepository->find($userId);
        $listing = $this->listingRepository->findOneBy([
            'id' => $listingId,
        ]);
        if (!$user->getFavoriteListings()->contains($listing)) {
            $user->addFavoriteListing($listing);
        } else {
            $user->removeFavoriteListing($listing);
        }

        $this->entityManager->flush();

        return true;
    }
}