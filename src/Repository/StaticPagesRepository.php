<?php

namespace App\Repository;

use App\Entity\StaticPages;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method StaticPages|null find($id, $lockMode = null, $lockVersion = null)
 * @method StaticPages|null findOneBy(array $criteria, array $orderBy = null)
 * @method StaticPages[]    findAll()
 * @method StaticPages[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StaticPagesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StaticPages::class);
    }

    // /**
    //  * @return StaticPages[] Returns an array of StaticPages objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?StaticPages
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
