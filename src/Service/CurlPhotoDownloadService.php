<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 28.09.2020
 *
 * @package estateblock20
 */

namespace App\Service;


use App\Service\Listing\ListingImageResizeService;
use Curl\Curl;
use mysql_xdevapi\Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;

class CurlPhotoDownloadService
{
    private Filesystem $filesystem;
    private LoggerInterface $logger;
    private ListingImageResizeService $imageResizeService;

    public function __construct(Filesystem $filesystem, LoggerInterface $logger, ListingImageResizeService $imageResizeService)
    {
        $this->filesystem = $filesystem;
        $this->logger = $logger;
        $this->imageResizeService = $imageResizeService;
    }


    public function photoDownload(array $photoUrls, string $destination, string $baseFileName): array
    {
        $curl = new Curl();

        $photosCounter = 1;
        $photoNamesArray = [];
        foreach ( $photoUrls as $photoUrl ) {
            try {
                $curl->get($photoUrl);
                $im = imagecreatefromstring($curl->getResponse());
                $fullFileName = $destination . $baseFileName . '-' . $photosCounter . '.jpg';
                if(imagejpeg($im, $fullFileName)) {
                    if ( file_exists($fullFileName) && ( imagesx($im) > 1200 || imagesy($im) > 1200 ) ) {
                        $this->imageResizeService->resizeImage($fullFileName);
                    }
                    $photoNamesArray[ $photosCounter ] = $baseFileName . '-' . $photosCounter . '.jpg';
                } else {
                    $this->logger->error('Fail-create-image :: ' . $fullFileName);
                }
            } catch ( \Exception $e ) {
                $this->logger->error($e->getMessage());
                $this->logger->error('Fail to process :: ' . $photoUrl);
            } finally {
                $photosCounter++;
            }
        }
        $curl->close();
        if (count($photoUrls) > 0 && count($photoNamesArray) == 0) {
            throw new \Exception("Could not download any photos for urls");
        }
        return $photoNamesArray;
    }

}