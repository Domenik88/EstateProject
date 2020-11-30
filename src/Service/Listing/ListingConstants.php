<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 28.09.2020
 *
 * @package estateblock20
 */

namespace App\Service\Listing;

interface ListingConstants
{
    const NEW_LISTING_STATUS = 'new';
    const UPDATED_LISTING_STATUS = 'updated';
    const LIVE_LISTING_STATUS = 'live';
    const NONE_PROCESSING_LISTING_STATUS = 'none';
    const PROCESSING_PROCESSING_LISTING_STATUS = 'processing';
    const ERROR_PROCESSING_LISTING_STATUS = 'error';
    const UPLOAD_LISTING_PIC_PATH = '/estateblockPics/';
    const LIVING_AREA = [
        [ -999999, -1 ], // Any
        [ 0, 500 ],
        [ 501, 750 ],
        [ 751, 1000 ],
        [ 1001, 1250 ],
        [ 1251, 1500 ],
        [ 1501, 1750 ],
        [ 1751, 2000 ],
        [ 2001, 2500 ],
        [ 2501, 3000 ],
        [ 3001, 3500 ],
        [ 3501, 4000 ],
        [ 4001, 5000 ],
        [ 5001, 7500 ],
        [ 7501, 10000 ],
        [ 10001, 999999 ],
    ];
    const LOT_SIZE = [
        [ -999999, -1 ], // Any
        [ 0, 0 ],
        [ 1, 2000 ],
        [ 2001, 3000 ],
        [ 3001, 5000 ],
        [ 5001, 10890 ],
        [ 10891, 21780 ],
        [ 21781, 43560 ],
        [ 43561, 87120 ],
        [ 87121, 217800 ],
        [ 217801, 435600 ],
        [ 435601, 999999 ],
    ];
    const YEAR_BUILT = [
        [ 1800, 1969 ],
        [ 1970, 1979 ],
        [ 1980, 1989 ],
        [ 1990, 1999 ],
        [ 2000, 2009 ],
        [ 2010, 2020 ],
    ];
    const SEARCH_RADIUS = 3; //kilometers

}