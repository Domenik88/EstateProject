<?php

namespace App\Repository;

use App\Entity\ListingMaster;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ListingMaster|null find($id, $lockMode = null, $lockVersion = null)
 * @method ListingMaster|null findOneBy(array $criteria, array $orderBy = null)
 * @method ListingMaster[]    findAll()
 * @method ListingMaster[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ListingMasterRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManagerInterface;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManagerInterface)
    {
        $this->entityManagerInterface = $entityManagerInterface;
        parent::__construct($registry, ListingMaster::class);
    }

    public function insertMasterList(array $masterList = [])
    {
        $listingMaster = new ListingMaster();
        foreach ($masterList as $item) {
            $listingMaster->setFeedId('ddf');
            $listingMaster->setFeedListingId($item->getListingKey());
            $listingMaster->setUpdatedTime($item->getLastModifyDate());
            $this->entityManagerInterface->persist($listingMaster);
        }
        $this->entityManagerInterface->flush();
    }

}
