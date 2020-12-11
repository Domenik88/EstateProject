<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 16.09.2020
 *
 * @package estateblock20
 */

namespace App\Service\Feed;

use App\Entity\Listing;
use App\Service\CurlPhotoDownloadService;
use PHRETS\Parsers\XML;
use PHRETS\Session;
use PHRETS\Configuration;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class DdfService
{
    private LoggerInterface $logger;
    private ?Session $rets;
    private CurlPhotoDownloadService $curlPhotoDownloadService;

    public function __construct(LoggerInterface $logger, CurlPhotoDownloadService $curlPhotoDownloadService)
    {
        $this->logger = $logger;
        $this->curlPhotoDownloadService = $curlPhotoDownloadService;
        $this->rets = null;
    }

    public function connect()
    {
        if (!$this->rets) {
            $config = new Configuration;
            $config->setLoginUrl('http://data.crea.ca/Login.svc/Login')
                ->setUsername('qeoMsug6JDuY5VrxNT3CZJGq')
                ->setPassword('S0uuYshvCPegrzypREFO4gdN')
                ->setRetsVersion('1.7.2');

            $this->rets = new Session($config);
            $this->rets->Login();
        }
    }

    public function __destruct()
    {
        $this->logger->info('Disconnecting from RETS!');
        if ($this->rets) {
            $this->rets->Disconnect();
            $this->rets = null;
        }
    }

    public function searchUpdatedListings(\DateTimeInterface $date,$offset = null,$limit = 100)
    {
        try {
            $this->connect();
            $results = $this->rets->Search('Property', 'Property', 'LastUpdated=' . $date->format('Y-m-d\TH:i:s\Z'), [ 'Format' => 'COMPACT-DECODED', 'Limit' => $limit, 'Offset' => $offset ]);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $this->logger->error($e->getTraceAsString());
            throw $e;
        }
        $totalRecordsCount = $results->getTotalResultsCount();
        $nextRecordOffset = $offset + $results->getReturnedResultsCount();
        $moreAvailable = $results->isMaxRowsReached();

        return new SearchResult($moreAvailable, $results->toArray(), $nextRecordOffset, $totalRecordsCount);
    }

    public function getMasterList(): array
    {
        try {
            $this->connect();
            $results = $this->rets->Search('Property', 'Property', 'ID=*', [ 'Limit' => null ]);
            $this->logger->log(LogLevel::INFO, 'Incoming-Listings-Total-Count :: ' . $results->count());
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $this->logger->error($e->getTraceAsString());
            throw $e;
        }

        return array_map(array($this,'toMasterListItem'),$results->toArray());
    }

    public function getListingById($listingId): array
    {
        try {
            $this->connect();
            $result = $this->rets->Search('Property', 'Property', 'ID=' . $listingId);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $this->logger->error($e->getTraceAsString());
            throw $e;
        }

        return $result->toArray();
    }

    public function toMasterListItem(array $listItem): MasterListItem
    {
        return new MasterListItem($listItem['ListingKey'],\DateTime::createFromFormat('d/m/Y H:i:s A',$listItem['ModificationTimestamp']));
    }

    public function fetchListingPhotosFromFeed(Listing $listing, string $destination): array
    {
        try {
            $this->connect();
            $results = $this->rets->getObject('Property', 'LargePhoto', $listing->getFeedListingID());
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $this->logger->error($e->getTraceAsString());
            throw $e;
        }
        $result = $results[0]->getContent();
        $res = new XML();
        $tmp = $res->parse($result);
        $photoUrls = array_map([$this,'extractImageUrl'],(array)$tmp->DATA);
        $listingFullAddress = str_replace(' ', '-', preg_replace('/[^a-z\d]/ui', '-',$listing->getFullAddress()));
        $baseFilename = $listing->getMlsNum() . '-' . $listingFullAddress;
        $photoNamesArray = $this->curlPhotoDownloadService->photoDownload($photoUrls,$destination,$baseFilename);

        return $photoNamesArray;
    }

    public function extractImageUrl(string $imgDataString): array
    {
        return explode("\t",$imgDataString)[3];
    }

    public function getListingByFeedListingId($feedListingId): ?array
    {
        try {
            $this->connect();
            $result = $this->rets->Search('Property', 'Property', 'ID=' . $feedListingId, [ 'Limit' => null ]);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $this->logger->error($e->getTraceAsString());
            throw $e;
        }

        if ( !$result->first() ) {
            throw new \Exception("Listing record not found! listingFeedId: {$feedListingId}");
        }

        return $result->first()->toArray();
    }

}