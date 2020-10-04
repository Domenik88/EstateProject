<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 16.09.2020
 *
 * @package estateblock20
 */

namespace App\Service\Feed;

use App\Service\CurlPhotoDownloadService;
use PHRETS\Parsers\XML;
use PHRETS\Session;
use PHRETS\Configuration;
use Psr\Log\LoggerInterface;

class DdfService
{
    private LoggerInterface $logger;
    private ?Session $rets;
    private CurlPhotoDownloadService $curlPhotoDownloadService;

    public function __construct(LoggerInterface $logger, CurlPhotoDownloadService $curlPhotoDownloadService)
    {
        $this->logger = $logger;
        $this->curlPhotoDownloadService = $curlPhotoDownloadService;
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
        $moreAvailable = !$results->isMaxRowsReached();

        $this->rets->Disconnect();

        return new SearchResult($moreAvailable, $results->toArray(), $nextRecordOffset, $totalRecordsCount);
    }

    public function getMasterList(): array
    {
        $this->connect();

        $results = $this->rets->Search('Property', 'Property', 'ID=*',['Limit' => null]);
        $this->rets->Disconnect();

        return array_map(array($this,'toMasterListItem'),$results->toArray());
    }

    public function getListingById($listingId): array
    {
        $this->connect();

        $result = $this->rets->Search('Property', 'Property', 'ID=' . $listingId);
        $this->rets->Disconnect();

        return $result->toArray();
    }

    public function toMasterListItem(array $listItem): MasterListItem
    {
        return new MasterListItem($listItem['ListingKey'],\DateTime::createFromFormat('d/m/Y H:i:s A',$listItem['ModificationTimestamp']));
    }

    public function fetchListingPhotosFromFeed(string $listingFeedId, string $destination): array
    {
        $this->connect();
        $results = $this->rets->getObject('Property','LargePhoto',$listingFeedId);

        foreach ($results as $result) {
            $res = new XML();
            $tmp = $res->parse($result->getContent());
            $photoUrls = array_map([$this,'extractImageUrl'],(array)$tmp->DATA);
            $photoNamesArray = $this->curlPhotoDownloadService->photoDownload($photoUrls,$destination,$listingFeedId);
        }
        $this->rets->Disconnect();
        return $photoNamesArray;
    }

    public function extractImageUrl(string $imgDataString)
    {
        return explode("\t",$imgDataString)[3];
    }
}