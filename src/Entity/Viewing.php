<?php

namespace App\Entity;

use App\Repository\ViewingRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ViewingRepository::class)
 * @ORM\Table(name="viewing",
 *     indexes={
 *         @ORM\Index(name="viewing_user_id_idx", columns={"user_id"}),
 *         @ORM\Index(name="viewing_listing_id_idx", columns={"listing_id"}),
 *     },
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(name="viewing_user_id_listing_id_idx", columns={"user_id","listing_id"}),
 *     },
 * )
 */
class Viewing
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="viewings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Listing::class, inversedBy="viewings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $listing;

    /**
     * @ORM\Column(type="text")
     */
    private $status;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getListing(): ?Listing
    {
        return $this->listing;
    }

    public function setListing(?Listing $listing): self
    {
        $this->listing = $listing;

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
}
