<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 28.09.2020
 *
 * @package estateblock20
 */

namespace App\Service\Listing;


interface ListingInterface
{
    const NEW_LISTING_STATUS = 'new';
    const UPDATED_LISTING_STATUS = 'updated';
    const LIVE_LISTING_STATUS = 'live';
    const NONE_PROCESSING_LISTING_STATUS = 'none';
    const PROCESSING_PROCESSING_LISTING_STATUS = 'processing';
    const ERROR_PROCESSING_LISTING_STATUS = 'error';
}