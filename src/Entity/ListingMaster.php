<?php

namespace App\Entity;

use App\Repository\ListingMasterRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ListingMasterRepository::class)
 * @ORM\Table(name="listing_master",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="listing_master_feed_id_feed_listing_id_idx", columns={"feed_id", "feed_listing_id"})}
 *     )
 */
class ListingMaster
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
    private $feedId;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $feedListingId;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedTime;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFeedId(): ?string
    {
        return $this->feedId;
    }

    public function setFeedId(string $feedId): self
    {
        $this->feedId = $feedId;

        return $this;
    }

    public function getFeedListingId(): ?string
    {
        return $this->feedListingId;
    }

    public function setFeedListingId(string $feedListingId): self
    {
        $this->feedListingId = $feedListingId;

        return $this;
    }

    public function getUpdatedTime(): ?\DateTimeInterface
    {
        return $this->updatedTime;
    }

    public function setUpdatedTime(\DateTimeInterface $updatedTime): self
    {
        $this->updatedTime = $updatedTime;

        return $this;
    }
}
