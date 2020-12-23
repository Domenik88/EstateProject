<?php

namespace App\Repository;

use App\Entity\Listing;
use App\Service\Listing\ListingConstants;
use App\Service\Listing\ListingCriteria;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

/**
 * @method Listing|null find( $id, $lockMode = null, $lockVersion = null )
 * @method Listing|null findOneBy( array $criteria, array $orderBy = null )
 * @method Listing[]    findAll()
 * @method Listing[]    findBy( array $criteria, array $orderBy = null, $limit = null, $offset = null )
 */
class ListingRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;
    private float $latitude;
    private float $longtitude;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        parent::__construct($registry, Listing::class);
    }

    public function count(array $criteria)
    {
        return parent::count($criteria); // TODO: Change the autogenerated stub
    }

    public function deleteListings(string $feedId)
    {
        $query = $this->getEntityManager()->createQuery("UPDATE App\Entity\Listing l SET l.deletedDate = current_timestamp() where l.feedID = :feedId and l.deletedDate IS NULL and not exists (select lm.feedListingId from App\Entity\ListingMaster lm where lm.feedListingId = l.feedListingID and lm.feedId = l.feedID)");
        $query->setParameter('feedId', $feedId);
        $query->execute();
    }

    public function createMissingListingsFromDdfListingMaster()
    {
        $rsm = new ResultSetMapping();
        $this->getEntityManager()->createNativeQuery("insert into listing(feed_id,feed_listing_id,status,processing_status) 
                select lm.feed_id, lm.feed_listing_id, 'new' as status,'none' as processing_status 
                from listing_master lm on conflict (feed_id,feed_listing_id) do update set last_update_from_feed = excluded.last_update_from_feed", $rsm)->execute();
    }

    public function getAllListingsInMapBox(float $neLat, float $neLng, float $swLat, float $swLng): array
    {
        $boxString = "box '((" . $neLat . ", " . $neLng . "),(" . $swLat . ", " . $swLng . "))'";
        try {
            $rsm = new ResultSetMappingBuilder($this->entityManager);
            $rsm->addRootEntityFromClassMetadata('App\Entity\Listing', 'l');
            $sql = "select * from listing where status IN ('" . ListingConstants::LIVE_LISTING_STATUS . "', '" . ListingConstants::UPDATED_LISTING_STATUS . "') and processing_status != '" . ListingConstants::ERROR_PROCESSING_LISTING_STATUS . "' and coordinates IS NOT NULL and coordinates <@ $boxString AND deleted_date IS NULL";
            $query = $this->entityManager->createNativeQuery($sql, $rsm);
            return $query->getResult();
        } catch ( \Exception $e ) {
            $this->logger->error($e->getMessage());
            $this->logger->error($e->getTraceAsString());
            return [];
        }
    }

    public function getSimilarListings(?string $type, ?string $ownershipType, ?int $bedRooms, ?array $livingAreaRange, ?array $lotSizeRange, ?array $yearBuiltRange, ?object $coordinates, ?string $mlsNum): array
    {
        try {
            $this->latitude = $coordinates->lat;
            $this->longtitude = $coordinates->lng;
            $sqlArray = [];
            $params = [];
            $rsm = new ResultSetMappingBuilder($this->entityManager);
            $rsm->addRootEntityFromClassMetadata('App\Entity\Listing', 'l');
            if ( $type ) {
                $sqlArray[] = 'type = :propertyType';
                $params[ 'propertyType' ] = $type;
            }
            if ( $ownershipType ) {
                $sqlArray[] = 'ownership_type = :ownershipType';
                $params[ 'ownershipType' ] = $ownershipType;
            }
            if ( isset($bedRooms) ) {
                $sqlArray[] = 'bedrooms = :bedroomsCount';
                $params[ 'bedroomsCount' ] = $bedRooms;
            }
            if ( $livingAreaRange ) {
                $sqlArray[] = 'living_area <@ int4range(:livingAreaFrom,:livingAreaTo)';
                $params[ 'livingAreaFrom' ] = $livingAreaRange[ 0 ];
                $params[ 'livingAreaTo' ] = $livingAreaRange[ 1 ];
            }
            if ( $lotSizeRange ) {
                if ( $lotSizeRange[ 0 ] != $lotSizeRange[ 1 ] ) {
                    $sqlArray[] = 'lot_size <@ int4range(:lotSizeFrom,:lotSizeTo)';
                    $params[ 'lotSizeFrom' ] = $lotSizeRange[ 0 ];
                    $params[ 'lotSizeTo' ] = $lotSizeRange[ 1 ];
                } else {
                    $sqlArray[] = 'lot_size = :lotSize';
                    $params[ 'lotSize' ] = $lotSizeRange[ 0 ];
                }
            }
            if ( $yearBuiltRange ) {
                $sqlArray[] = 'year_built <@ int4range(:yearBuiltFrom,:yearBuiltTo)';
                $params[ 'yearBuiltFrom' ] = $yearBuiltRange[ 0 ];
                $params[ 'yearBuiltTo' ] = $yearBuiltRange[ 1 ];
            }
            $sqlArray[] = 'circle (\'(' . $this->latitude . ',' . $this->longtitude . ')\',' . ListingConstants::SEARCH_RADIUS / 100 . ') @> coordinates';
            if ( isset($mlsNum) ) {
                $sqlArray[] = 'mls_num != :mlsNumber';
                $params[ 'mlsNumber' ] = $mlsNum;
            }
            $sql = "select * from listing where status IN ('" . ListingConstants::LIVE_LISTING_STATUS . "', '" . ListingConstants::UPDATED_LISTING_STATUS . "') and deleted_date is null";
            if ( !empty($sqlArray) ) {
                $sql .= ' and ' . implode(' and ', $sqlArray);
            }
            $query = $this->entityManager->createNativeQuery($sql, $rsm);
            foreach ( $params as $key => $param ) {
                $query->setParameter($key, $param);
            }
            return $query->getResult();
        } catch ( \Exception $e ) {
            $this->logger->error($e->getMessage());
            $this->logger->error($e->getTraceAsString());
            return [];
        }
    }

    public function getCounters(array $cities, string $stateOrProvince, string $feedId): ?array
    {
        return $this->createQueryBuilder('l')
            ->select('l.city, COUNT(l.mlsNum) as counter')
            ->where("l.city IN (:sities)")
            ->andWhere('l.deletedDate IS NULL')
            ->andWhere('l.stateOrProvince = :stateOrProvince')
            ->andWhere("l.status IN (:statuses)")
            ->andWhere('l.feedID = :feedID')
            ->groupBy('l.city')
            ->setParameter('sities', $cities)
            ->setParameter('stateOrProvince', $stateOrProvince)
            ->setParameter('statuses', [ ListingConstants::LIVE_LISTING_STATUS, ListingConstants::UPDATED_LISTING_STATUS ])
            ->setParameter('feedID', $feedId)
            ->getQuery()
            ->getResult()
            ;
    }

    public function getListingsByCriteria(ListingCriteria $criteria, int $page = 1, int $pageSize = 50)
    {
        $rsm = new ResultSetMappingBuilder($this->entityManager);
        $rsm->addRootEntityFromClassMetadata('App\Entity\Listing', 'l');
        $query = $this->entityManager->createNativeQuery("SELECT l.* FROM listing l
                WHERE l.feed_id = :feedId 
                    AND l.deleted_date IS NULL 
                    AND l.status IN (:statuses) 
                ORDER BY l.contract_date DESC NULLS LAST 
                LIMIT :limit 
                OFFSET :offset",
            $rsm);
        $query->setParameter('feedId', $criteria->feedId);
        $query->setParameter('statuses', $criteria->statuses);
        $query->setParameter('limit', $pageSize);
        $query->setParameter('offset', ($page - 1) * $pageSize);
        return $query->getResult();
    }

}
