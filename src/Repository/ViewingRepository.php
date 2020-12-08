<?php

namespace App\Repository;

use App\Entity\Viewing;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Viewing|null find($id, $lockMode = null, $lockVersion = null)
 * @method Viewing|null findOneBy(array $criteria, array $orderBy = null)
 * @method Viewing[]    findAll()
 * @method Viewing[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ViewingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Viewing::class);
    }
}
