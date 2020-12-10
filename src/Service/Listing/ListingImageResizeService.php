<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 09.12.2020
 *
 * @package estateblock20
 */

namespace App\Service\Listing;

use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Imagick\Imagine;

class ListingImageResizeService
{

    public function resizeImage(string $path)
    {
        $imagine = new Imagine();
        $size = new Box(1200,1200);
        $mode = ImageInterface::THUMBNAIL_INSET;
        $imagine->open($path)
            ->thumbnail($size,$mode)
            ->save($path);
    }
}