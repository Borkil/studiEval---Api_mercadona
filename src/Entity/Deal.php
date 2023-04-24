<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\DealRepository;
use DateTime;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DealRepository::class)]
class Deal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['product:read', 'category:read'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['product:read', 'category:read'])]
    private ?\DateTime $createdAt = null;

    #[ORM\Column]
    #[Groups(['product:read', 'category:read'])]
    private ?\DateTime $updatedAt = null;

    #[ORM\Column]
    #[Groups(['product:read', 'category:read'])]
    private ?\DateTime $StartedAt = null;

    #[ORM\Column]
    #[Groups(['product:read', 'category:read'])]
    private ?\DateTime $finishedAt = null;

    #[ORM\Column]
    #[Assert\Range(max: 99, min: 1 )]
    #[Groups(['product:read', 'category:read'])]
    private ?int $percentage = null;

    #[ORM\Column()]
    #[Assert\Positive()]
    #[Groups(['product:read', 'category:read'])]
    private ?float $dealPrice = null;

    #[ORM\ManyToOne(inversedBy: 'deals')]
    private ?Product $Product = null;
    
    public function __construct ()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getStartedAt(): ?\DateTime
    {
        return $this->StartedAt;
    }

    public function setStartedAt(\DateTime $StartedAt): self
    {
        $this->StartedAt = $StartedAt;

        return $this;
    }

    public function getFinishedAt(): ?\DateTime
    {
        return $this->finishedAt;
    }

    public function setFinishedAt(\DateTime $finishedAt): self
    {
        $this->finishedAt = $finishedAt;

        return $this;
    }

    public function getPercentage(): ?int
    {
        return $this->percentage;
    }

    public function setPercentage(int $percentage): self
    {
        $this->percentage = $percentage;

        return $this;
    }

    public function getDealPrice(): ?string
    {
        return $this->dealPrice;
    }

    public function setDealPrice(string $dealPrice): self
    {
        $this->dealPrice = $dealPrice;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->Product;
    }

    public function setProduct(?Product $Product): self
    {
        $this->Product = $Product;

        return $this;
    }
}
