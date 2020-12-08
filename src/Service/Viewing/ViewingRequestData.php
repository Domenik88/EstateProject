<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 07.12.2020
 *
 * @package estateblock20
 */

namespace App\Service\Viewing;

class ViewingRequestData
{

    private string $name;
    private string $email;
    private string $phone;
    private string $listingId;

    public function __construct(string $name, string $email, string $phone, string $listingId)
    {
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
        $this->listingId = $listingId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @return string
     */
    public function getListingId(): string
    {
        return $this->listingId;
    }
}