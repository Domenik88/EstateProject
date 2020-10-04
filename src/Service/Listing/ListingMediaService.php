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

class ListingMediaService
{

    public function getListingPhotos(Listing $listing): array
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

}