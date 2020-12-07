<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 07.12.2020
 *
 * @package estateblock20
 */

namespace App\Service\Viewing;

class ViewingFormDataFormatter
{

    public string $name;
    public string $email;
    public string $phone;
    public string $listingId;

    public function __construct(string $name, string $email, string $phone, string $listingId)
    {
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
        $this->listingId = $listingId;
    }
}