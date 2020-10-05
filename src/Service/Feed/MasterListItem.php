<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 16.09.2020
 *
 * @package estateblock20
 */

namespace App\Service\Feed;


use DateTimeInterface;

class MasterListItem
{
    public string $listingKey;
    public DateTimeInterface $lastModifyDate;

    public function __construct(string $listingKey, DateTimeInterface $lastModifyDate)
    {
        $this->listingKey = $listingKey;
        $this->lastModifyDate = $lastModifyDate;
    }

    public function getListingKey(): ?string
    {
        return $this->listingKey;
    }

    public function getLastModifyDate(): DateTimeInterface
    {
        return $this->lastModifyDate;
    }
}