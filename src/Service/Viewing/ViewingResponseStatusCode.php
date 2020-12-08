<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 07.12.2020
 *
 * @package estateblock20
 */

namespace App\Service\Viewing;

class ViewingResponseStatusCode
{
    public int $statusCode;
    public string $statusMessage;

    public function __construct(int $statusCode, string $statusMessage)
    {
        $this->statusCode = $statusCode;
        $this->statusMessage = $statusMessage;
    }
}