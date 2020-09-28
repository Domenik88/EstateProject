<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 28.09.2020
 *
 * @package estateblock20
 */

namespace App\Service;


use App\Service\Listing\ListingInterface;
use Curl\Curl;
use Symfony\Component\Filesystem\Filesystem;

class CurlPhotoDownloadService
{
    private string $localPath;
    private Filesystem $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function init(string $fileName, string $feedId)
    {
        $curl = new Curl();
        $curl->setOpt(CURLOPT_RETURNTRANSFER,true);
        $this->localPath = sys_get_temp_dir() . ListingInterface::UPLOAD_LISTING_PIC_PATH . $feedId . '/' . $fileName . '/';
        if (!is_dir($this->localPath)) {
            $this->filesystem->mkdir($this->localPath);
        }
        return $curl;
    }

    public function photoDownload(array $photos, string $fileName, string $feedId): array
    {
        $curl = $this->init($fileName,$feedId);
        $photosCounter = 1;
        $photoNamesArray = [];
        foreach ( $photos as $photo ) {
            $curl->get($photo);
            $im = @imagecreatefromstring($curl->getResponse());
            $fullFileName = $this->localPath.$fileName.'_'.$photosCounter.'.'.$this->getFileExtention($curl->getResponseHeaders('content-type'));
            @imagejpeg($im, $fullFileName, 100);
            $this->filesystem->chmod($fullFileName,0644);
            $photoNamesArray[] = $fileName.'_'.$photosCounter;
            $photosCounter++;
        }
        $curl->close();
        dump($photoNamesArray);
        return $photoNamesArray;
    }

    private function getFileExtention($cType)
    {
        switch ($cType) {
            case "image/jpeg" :
            case "image/jpg" : $fileExtention = 'jpg'; break;
            case "image/gif" : $fileExtention = 'gif'; break;
            case "image/png" : $fileExtention = 'png'; break;
            default : $fileExtention = 'jpg';
        }
        return $fileExtention;
    }
}