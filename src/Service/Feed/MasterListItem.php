<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 16.09.2020
 *
 * @package estateblock20
 */

namespace App\Service\Feed;


class MasterListItem
{
    public string $listingKey;
    public string $lastModifyDate;
    public function __construct(string $listingKey, string $lastModifyDate)
    {
        $this->listingKey = $listingKey;
        $this->lastModifyDate = $lastModifyDate;
    }
}