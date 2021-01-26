<?php

namespace App\Repository;

use App\Entity\School;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

/**
 * @method School|null find($id, $lockMode = null, $lockVersion = null)
 * @method School|null findOneBy(array $criteria, array $orderBy = null)
 * @method School[]    findAll()
 * @method School[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SchoolRepository extends ServiceEntityRepository
{

    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        parent::__construct($registry, School::class);
    }

    public function truncateSchoolTable()
    {
        $rsm = new ResultSetMapping();
        $this->entityManager->createNativeQuery('TRUNCATE TABLE school RESTART IDENTITY',$rsm)->execute();
    }

    public function getPublicSchools($listingCoordinates)
    {
        $rsm = new ResultSetMappingBuilder($this->entityManager);
        $rsm->addRootEntityFromClassMetadata('App\Entity\School', 's');
        $sql = "select * from school where point (:lat, :lng) <@ areas and public = true";
        $query = $this->entityManager->createNativeQuery($sql, $rsm);
        $query->setParameter('lat', $listingCoordinates->getLatitude());
        $query->setParameter('lng', $listingCoordinates->getLongitude());
        return $query->getResult();
    }

    public function getPrivateSchools($listingCoordinates)
    {
        $rsm = new ResultSetMappingBuilder($this->entityManager);
        $rsm->addRootEntityFromClassMetadata('App\Entity\School', 's');
        $sql = "select *, point (:lat, :lng) <-> coordinates as distance from school where level LIKE '%Elementary%' and public = false order by distance asc limit 1";
        $query = $this->entityManager->createNativeQuery($sql, $rsm);
        $query->setParameter('lat', $listingCoordinates->getLatitude());
        $query->setParameter('lng', $listingCoordinates->getLongitude());
        $privateSchools['elementary'] = $query->getResult();

        $rsm = new ResultSetMappingBuilder($this->entityManager);
        $rsm->addRootEntityFromClassMetadata('App\Entity\School', 's');
        $sql = "select *, point (:lat, :lng) <-> coordinates as distance from school where level LIKE '%Secondary%' and public = false order by distance asc limit 1";
        $query = $this->entityManager->createNativeQuery($sql, $rsm);
        $query->setParameter('lat', $listingCoordinates->getLatitude());
        $query->setParameter('lng', $listingCoordinates->getLongitude());
        $privateSchools['secondary'] = $query->getResult();

        return $privateSchools;
    }

    public function getAllSchoolsInMapBox(float $neLat, float $neLng, float $swLat, float $swLng): array
    {
        $boxString = "box '((" . $neLat . ", " . $neLng . "),(" . $swLat . ", " . $swLng . "))'";
        try {
            $rsm = new ResultSetMappingBuilder($this->entityManager);
            $rsm->addRootEntityFromClassMetadata('App\Entity\School', 's');
            $sql = "select * from school where coordinates <@ $boxString";
            $query = $this->entityManager->createNativeQuery($sql, $rsm);
            return $query->getResult();
        } catch ( \Exception $e ) {
            $this->logger->error($e->getMessage());
            $this->logger->error($e->getTraceAsString());
            return [];
        }
    }

}
