<?php

namespace App\Entity;

use App\Repository\ListingRepository;
use App\Service\Geo\Point;
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
     * @ORM\Column(type="string", length=20, options={"default":"new"})
     */
    private $status = 'new';

    /**
     * @ORM\Column(type="string", length=20, options={"default":"none"})
     */
    private $processingStatus = 'none';

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastUpdateFromFeed;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $imagesData;

    /**
     * @ORM\Column(type="point", nullable=true)
     * @var Point
     */
    private $coordinates;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $rawData;

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getProcessingStatus(): ?string
    {
        return $this->processingStatus;
    }

    public function setProcessingStatus(string $processingStatus): self
    {
        $this->processingStatus = $processingStatus;

        return $this;
    }

    public function getLastUpdateFromFeed(): ?\DateTimeInterface
    {
        return $this->lastUpdateFromFeed;
    }

    public function setLastUpdateFromFeed(?\DateTimeInterface $lastUpdateFromFeed): self
    {
        $this->lastUpdateFromFeed = $lastUpdateFromFeed;

        return $this;
    }
    public function getImagesData(): array
    {
        return $this->imagesData;
    }

    public function setImagesData(object $imagesData): self
    {
        $this->imagesData = $imagesData;

        return $this;
    }

    public function getCoordinates(): Point
    {
        return $this->coordinates;
    }

    public function setCoordinates(Point $coordinates): self
    {
        $this->coordinates = $coordinates;

        return $this;
    }

    public function getRawData()
    {
        return $this->rawData;
    }

    public function setRawData(?object $rawData): self
    {
        $this->rawData = $rawData;

        return $this;
    }
}
