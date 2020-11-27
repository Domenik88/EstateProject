<?php

namespace App\Repository;

use App\Entity\ListingMaster;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
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
    const INSERT_LISTING_MASTER_CHUNK_SIZE = 1000;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManagerInterface)
    {
        $this->entityManager = $entityManagerInterface;
        parent::__construct($registry, ListingMaster::class);
    }

    public function insertMasterList(array $masterList = [])
    {
        $chunksMasterList = array_chunk($masterList,self::INSERT_LISTING_MASTER_CHUNK_SIZE);
        foreach ( $chunksMasterList as $items ) {
            $values = array_fill(0,count($items),"(?,?,?)");
            $valuesForQuery = implode(",",$values);

            $sql = "insert into listing_master(feed_id,feed_listing_id,updated_time)
        values {$valuesForQuery}
        on conflict (feed_id,feed_listing_id)
        do update set updated_time = EXCLUDED.updated_time";
            $rsm = new ResultSetMapping();
            $query = $this->entityManager->createNativeQuery($sql,$rsm);
            $paramCounter = 1;
            foreach ( $items as $item ) {
                $query->setParameter($paramCounter++,'ddf');
                $query->setParameter($paramCounter++,$item->getListingKey());
                $query->setParameter($paramCounter++,$item->getLastModifyDate());
            }
            $query->execute();
        }
    }

    public function truncateListingMasterTable()
    {
        $rsm = new ResultSetMapping();
        $this->entityManager->createNativeQuery('TRUNCATE TABLE listing_master RESTART IDENTITY',$rsm)->execute();
    }

}
