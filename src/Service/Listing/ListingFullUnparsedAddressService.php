<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 13.10.2020
 *
 * @package estateblock20
 */

namespace App\Service\Listing;

use App\Entity\Listing;

class ListingFullUnparsedAddressService
{

    public function getListingFullUnparsedAddress(Listing $listing): string
    {
        return rtrim($listing->getUnparsedAddress(),",") . ', ' . rtrim($listing->getCity(), ",") . ', ' . rtrim($listing->getStateOrProvince(), ",") . ' ' . rtrim($listing->getPostalCode(), ",");
    }

}