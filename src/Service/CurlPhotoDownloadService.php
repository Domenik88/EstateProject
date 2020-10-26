<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 28.09.2020
 *
 * @package estateblock20
 */

namespace App\Service;


use Curl\Curl;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;

class CurlPhotoDownloadService
{
    private Filesystem $filesystem;
    private LoggerInterface $logger;

    public function __construct(Filesystem $filesystem, LoggerInterface $logger)
    {
        $this->filesystem = $filesystem;
        $this->logger = $logger;
    }


    public function photoDownload(array $photoUrls, string $destination, string $baseFileName): array
    {
        $curl = new Curl();

        if ( !is_dir($destination) ) {
            $this->filesystem->mkdir($destination);
        }
        $photosCounter = 1;
        $photoNamesArray = [];
        foreach ( $photoUrls as $photoUrl ) {
            try {
                $curl->get($photoUrl);
                $im = imagecreatefromstring($curl->getResponse());
                $fullFileName = $destination . $baseFileName . '_' . $photosCounter . '.jpg';
                imagejpeg($im, $fullFileName, 100);
                $photoNamesArray[$photosCounter] = $baseFileName . '_' . $photosCounter . '.jpg';
                $photosCounter++;
            } catch ( \Exception $e ) {
                $this->logger->error($e->getMessage());
                $this->logger->error('imagecreatefromstring::' . $photoUrl);
                throw $e;
            }
        }
        $curl->close();
        return $photoNamesArray;
    }

}