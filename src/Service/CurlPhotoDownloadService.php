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
use Symfony\Component\Filesystem\Filesystem;

class CurlPhotoDownloadService
{
    private Filesystem $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }


    public function photoDownload(array $photoUrls, string $destination, string $baseFileName): array
    {
        $curl = new Curl();

        if (!is_dir($destination)) {
            $this->filesystem->mkdir($destination);
        }
        $photosCounter = 1;
        $photoNamesArray = [];
        foreach ( $photoUrls as $photoUrl ) {
            $curl->get($photoUrl);
            $im = imagecreatefromstring($curl->getResponse());

            $fullFileName = $destination.$baseFileName.'_'.$photosCounter.'.jpg';

            imagejpeg($im, $fullFileName, 100);

            $photoNamesArray[$photosCounter] = $baseFileName.'_'.$photosCounter.'.jpg';
            $photosCounter++;
        }
        $curl->close();
        return $photoNamesArray;
    }

}