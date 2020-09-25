<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 16.09.2020
 *
 * @package estateblock20
 */

namespace App\Service\Feed;

use PHRETS\Session;
use PHRETS\Configuration;
use Psr\Log\LoggerInterface;

class DdfService
{
    private LoggerInterface $logger;
    private ?Session $rets;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function connect()
    {
            $config = new Configuration;
            $config->setLoginUrl('http://data.crea.ca/Login.svc/Login')
                ->setUsername('qeoMsug6JDuY5VrxNT3CZJGq')
                ->setPassword('S0uuYshvCPegrzypREFO4gdN')
                ->setRetsVersion('1.7.2');

            $this->rets = new Session($config);
            $this->rets->Login();
    }

    public function searchUpdatedListings(\DateTimeInterface $date,$offset = null,$limit = 100)
    {
        $this->connect();
        $results = $this->rets->Search('Property', 'Property', 'LastUpdated=' . $date->format('Y-m-d\TH:i:s\Z'),['Format' => 'COMPACT-DECODED','Limit' => $limit, 'Offset' => $offset]);
        $totalRecordsCount = $results->getTotalResultsCount();
        $nextRecordOffset = $offset + $results->getReturnedResultsCount();
        $moreAvailable = $nextRecordOffset < $totalRecordsCount;
        $this->rets->Disconnect();

        return new SearchResult($moreAvailable, $results->toArray(), $nextRecordOffset, $totalRecordsCount);
    }

    public function getMasterList($limit = null): array
    {
        $this->connect();

        $results = $this->rets->Search('Property', 'Property', 'ID=*',['Limit' => $limit, 'Offset'=>1]);
        return array_map(array($this,'toMasterListItem'),$results->toArray());
    }

    public function getListingById($listingId)
    {
        $this->connect();
        $result = $this->rets->Search('Property', 'Property', 'ID=' . $listingId);
        return $result->toArray();
    }

    public function toMasterListItem(array $listItem): MasterListItem
    {
        return new MasterListItem($listItem['ListingKey'],$listItem['ModificationTimestamp']);
    }

    public function hello()
    {
        $this->logger->alert('Petya');
        return 'Say Hello!';
    }
}