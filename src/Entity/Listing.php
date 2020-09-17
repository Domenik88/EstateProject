<?php

namespace App\Entity;

use App\Repository\ListingRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ListingRepository::class)
 */
class Listing
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $MLS_NUM;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $FeedListingID;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $FeedID;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $ListPrice;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $PostalCode;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $PhotosCount;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $UnparsedAddress;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $City;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMLSNUM(): ?string
    {
        return $this->MLS_NUM;
    }

    public function setMLSNUM(string $MLS_NUM): self
    {
        $this->MLS_NUM = $MLS_NUM;

        return $this;
    }

    public function getFeedListingID(): ?string
    {
        return $this->FeedListingID;
    }

    public function setFeedListingID(string $FeedListingID): self
    {
        $this->FeedListingID = $FeedListingID;

        return $this;
    }

    public function getFeedID(): ?string
    {
        return $this->FeedID;
    }

    public function setFeedID(string $FeedID): self
    {
        $this->FeedID = $FeedID;

        return $this;
    }

    public function getListPrice(): ?float
    {
        return $this->ListPrice;
    }

    public function setListPrice(?float $ListPrice): self
    {
        $this->ListPrice = $ListPrice;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->PostalCode;
    }

    public function setPostalCode(?string $PostalCode): self
    {
        $this->PostalCode = $PostalCode;

        return $this;
    }

    public function getPhotosCount(): ?int
    {
        return $this->PhotosCount;
    }

    public function setPhotosCount(?int $PhotosCount): self
    {
        $this->PhotosCount = $PhotosCount;

        return $this;
    }

    public function getUnparsedAddress(): ?string
    {
        return $this->UnparsedAddress;
    }

    public function setUnparsedAddress(?string $UnparsedAddress): self
    {
        $this->UnparsedAddress = $UnparsedAddress;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->City;
    }

    public function setCity(?string $City): self
    {
        $this->City = $City;

        return $this;
    }
}
