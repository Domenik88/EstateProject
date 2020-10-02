<?php

namespace App\Repository;

use App\Entity\ListingMaster;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ListingMaster|null find($id, $lockMode = null, $lockVersion = null)
 * @method ListingMaster|null findOneBy(array $criteria, array $orderBy = null)
 * @method ListingMaster[]    findAll()
 * @method ListingMaster[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ListingMasterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ListingMaster::class);
    }

    // /**
    //  * @return ListingMaster[] Returns an array of ListingMaster objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ListingMaster
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
