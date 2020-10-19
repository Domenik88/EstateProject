<?php

namespace App\Entity;

use App\Repository\ListingRepository;
use App\Service\Geo\Point;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ListingRepository::class)
 * @ORM\Table(name="listing",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(name="listing_mls_num_feed_id_state_or_province_idx", columns={"mls_num", "feed_id", "state_or_province"}, options={"where": "((state_or_province IS NOT NULL) AND ((status)::text = 'live'::text) AND (mls_num IS NOT NULL))"}),
 *          @ORM\UniqueConstraint(name="listing_feed_id_feed_listing_id_idx", columns={"feed_id", "feed_listing_id"})
 *     },
 * )
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
     * @ORM\Column(type="string", length=20, nullable=true)
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
     * @ORM\Column(type="json_array", nullable=true, options={"jsonb":true})
     */
    private $imagesData = [];

    /**
     * @ORM\Column(type="point", nullable=true)
     * @var Point
     */
    private $coordinates;

    /**
     * @ORM\Column(type="json_array", nullable=true, options={"jsonb":true})
     */
    private $rawData = [];

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deletedDate;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $stateOrProvince;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $country;

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
    public function getImagesData(): ?array
    {
        return $this->imagesData;
    }

    public function setImagesData(?array $imagesData): self
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

    public function getRawData(): ?array
    {
        return $this->rawData;
    }

    public function setRawData(?array $rawData): self
    {
        $this->rawData = $rawData;

        return $this;
    }

    public function getDeletedDate(): ?\DateTimeInterface
    {
        return $this->deletedDate;
    }

    public function setDeletedDate(?\DateTimeInterface $deletedDate): self
    {
        $this->deletedDate = $deletedDate;

        return $this;
    }

    public function getStateOrProvince(): ?string
    {
        return $this->stateOrProvince;
    }

    public function setStateOrProvince(?string $stateOrProvince): self
    {
        $this->stateOrProvince = $stateOrProvince;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getFullAddress(): ?string
    {
        $addressArray = [];
        $unparsedAddress = rtrim($this->getUnparsedAddress(),",");
        if (!empty($unparsedAddress)) { $addressArray[] = $unparsedAddress; }
        $city = rtrim($this->getCity(), ",");
        if (!empty($city)) { $addressArray[] = $city; }
        $stateOrProvince = rtrim($this->getStateOrProvince(), ",");
        if (!empty($stateOrProvince)) { $addressArray[] = $stateOrProvince; }
        $postalCode = rtrim($this->getPostalCode(), ",");
        if (!empty($postalCode)) { $addressArray[] = $postalCode; }
        if (count($addressArray) > 0) {
            return implode(", ", $addressArray);
        } else {
            return null;
        }
    }

    public function getDataForMap()
    {
        $return = [];
        $return['mlsNum'] = $this->getMlsNum();
        $return['address'] = $this->getFullAddress();
        $return['lat'] = $this->getCoordinates()->getLatitude();
        $return['lng'] = $this->getCoordinates()->getLongitude();

        return $return;
    }
}