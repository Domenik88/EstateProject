<?php

namespace App\Entity;

use App\Repository\ListingRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ListingRepository::class)
 * @ORM\Table(name="listing",uniqueConstraints={@ORM\UniqueConstraint(name="listing_mls_num_feed_listing_id_idx", columns={"mls_num", "feed_listing_id"})})
 */
class Listing
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $mlsNum;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $feedListingID;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $feedID;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $listPrice;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $postalCode;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $photosCount;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $unparsedAddress;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $imagesData = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMlsNum(): ?string
    {
        return $this->mlsNum;
    }

    public function setMlsNum(string $mlsNum): self
    {
        $this->mlsNum = $mlsNum;

        return $this;
    }

    public function getFeedListingID(): ?string
    {
        return $this->feedListingID;
    }

    public function setFeedListingID(string $feedListingID): self
    {
        $this->feedListingID = $feedListingID;

        return $this;
    }

    public function getFeedID(): ?string
    {
        return $this->feedID;
    }

    public function setFeedID(string $feedID): self
    {
        $this->feedID = $feedID;

        return $this;
    }

    public function getListPrice(): ?float
    {
        return $this->listPrice;
    }

    public function setListPrice(?float $listPrice): self
    {
        $this->listPrice = $listPrice;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getPhotosCount(): ?int
    {
        return $this->photosCount;
    }

    public function setPhotosCount(?int $photosCount): self
    {
        $this->photosCount = $photosCount;

        return $this;
    }

    public function getUnparsedAddress(): ?string
    {
        return $this->unparsedAddress;
    }

    public function setUnparsedAddress(?string $unparsedAddress): self
    {
        $this->unparsedAddress = $unparsedAddress;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getImagesData(): ?array
    {
        return $this->imagesData;
    }

    public function setImagesData(?array $imagesData): self
    {
        $this->imagesData = $imagesData;

        return $this;
    }
}
