<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 30.12.2020
 *
 * @package estateblock20
 */

namespace App\Service\Listing;

class ListingBreadcrumbObject
{

    public string $crumb;
    public string $uri;

    public function __construct(string $crumb, string $uri)
    {
        $this->crumb = $crumb;
        $this->uri = $uri;
    }

}