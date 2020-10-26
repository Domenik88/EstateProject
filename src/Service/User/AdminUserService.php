<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 23.10.2020
 *
 * @package estateblock20
 */

namespace App\Service\User;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminUserService
{
    private UserPasswordEncoderInterface $encoder;
    private EntityManagerInterface $entityManager;

    public function __construct(UserPasswordEncoderInterface $encoder, EntityManagerInterface $entityManager)
    {
        $this->encoder = $encoder;
        $this->entityManager = $entityManager;
    }

    public function createNewAdminUser($user)
    {
        $user->setRoles(["ROLE_ADMIN"]);
        $user->setPassword($this->encoder->encodePassword($user, $user->getPassword()));
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function editAdminUser()
    {
        $this->entityManager->flush();
    }

    public function removeAdminUser($user)
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }
}