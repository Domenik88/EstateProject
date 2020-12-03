<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 29.09.2020
 *
 * @package estateblock20
 */

namespace App\Service\Listing;


use App\Entity\Listing;
use Izica\ProgressiveImages;

class ListingMediaService
{

    public function getListingPhotos(Listing $listing): ?array
    {
        $imageNames = $listing->getImagesData();
        $listingImagesUrlArray = [];
        if (!is_null($imageNames)) {
            $i = 1;
            while ( $i <= count($imageNames) ) {
                $listingImagesUrlArray[] = $_ENV['ESBL_DIGITAL_OCEAN_ENDPOINT_EDGE'] . '/listings/' . $listing->getFeedID() . '/' . $listing->getFeedListingID() . '/' . $imageNames[$i];
                $i++;
            }
        } else {
            $listingImagesUrlArray[] = $this->getListingNoImage();
        }
        return $listingImagesUrlArray;
    }

    public function getListingNoImage()
    {
        return $_ENV['ESBL_DIGITAL_OCEAN_ENDPOINT_EDGE'] . '/listings/' . 'no-img.jpg';
    }

    public function convertImage(string $sourcePath,string $destinationPath,string $filename)
    {
        $obData = ProgressiveImages::fromFileSource('/upload/iblock/fe7/fe7728c5f2c6763693eb1d9ef105c46c.png')
            ->setFileName('custom-file-name')
            ->setDestinationFolder($_SERVER['DOCUMENT_ROOT'] . '/test/cache/')
            ->convert();    }
}