<?php

namespace App\Entity;

use App\Repository\FeedRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FeedRepository::class)
 */
class Feed
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
    private $name;

    /**
     * @ORM\Column(type="boolean")
     */
    private $busy;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $lastRunTime;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function isBusy(): ?bool
    {
        return $this->busy;
    }

    public function setBusy(bool $busy): self
    {
        $this->busy = $busy;

        return $this;
    }

    public function getLastRunTime(): ?\DateTimeInterface
    {
        return $this->lastRunTime;
    }

    public function setLastRunTime(?\DateTimeInterface $lastRunTime): self
    {
        $this->lastRunTime = $lastRunTime;

        return $this;
    }
}
