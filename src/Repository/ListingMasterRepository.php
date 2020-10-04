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
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManagerInterface)
    {
        $this->entityManager = $entityManagerInterface;
        parent::__construct($registry, ListingMaster::class);
    }

    public function insertMasterList(array $masterList = [])
    {
        $batchSize = 1000;
        $batchCounter = 0;
        foreach ($masterList as $item) {
            $listingMaster = $this->findOneBy([
                'feedId' => 'ddf',
                'feedListingId' => $item->getListingKey(),
            ]);
            if (!$listingMaster) {
                $listingMaster = new ListingMaster();
            }
            $listingMaster->setFeedId('ddf');
            $listingMaster->setFeedListingId($item->getListingKey());
            $listingMaster->setUpdatedTime($item->getLastModifyDate());
            $this->entityManager->persist($listingMaster);
            $batchCounter++;
            if ($batchCounter >= $batchSize) {
                $this->entityManager->flush();
                $this->entityManager->clear();
            }
        }
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

}
