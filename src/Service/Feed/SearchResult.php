<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 16.09.2020
 *
 * @package estateblock20
 */

namespace App\Service\Feed;


class SearchResult
{
    public bool $moreAvailable;
    public array $results;
    public int $nextRecordOffset;

    public function __construct(bool $moreAvailable, array $results, int $nextRecordOffset)
    {
        $this->results = $results;
        $this->moreAvailable = $moreAvailable;
        $this->nextRecordOffset = $nextRecordOffset;
    }
}