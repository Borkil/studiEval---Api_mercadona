<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['product:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    #[Assert\Length(
        max: 150
    )]
    #[Assert\NotBlank()]
    #[Assert\NotNull]
    #[Groups(['product:read', 'product:create'])]
    private ?string $label = null;
    
    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank()]
    #[Assert\NotNull]
    #[Groups(['product:read', 'product:create'])]
    private ?string $description = null;
    
    #[ORM\Column(nullable: true)]
    #[Assert\Positive]
    #[Assert\NotNull]
    #[Groups(['product:read', 'product:create'])]
    private ?float $Price = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['product:read', 'product:create'])]
    private ?string $image = null;
    
    #[ORM\ManyToOne(inversedBy: 'product', cascade: ['persist'])]
    #[Groups(['product:read','product:create', 'product:read'])]
    private ?Category $category = null;

    #[ORM\Column(nullable: true)]
    #[Assert\When(
        expression: 'this.isIsDeal() === true',
        constraints: [
            new Assert\notNull()
        ]
    )]
    #[Assert\When(
        expression: 'this.isIsDeal() === false',
        constraints: [
            new Assert\IsNull()
        ]
    )]
    #[Groups(['product:read', 'product:create'])]
    private ?\DateTimeImmutable $finishDealAt = null;

    #[ORM\Column(nullable: true)]
    #[Assert\When(
        expression: 'this.isIsDeal() === true',
        constraints: [
            new Assert\notNull(),
            new Assert\Positive()
        ]
    )]
    #[Assert\When(
        expression: 'this.isIsDeal() === false',
        constraints: [
            new Assert\IsNull()
        ]
    )]
    #[Groups(['product:read', 'product:create'])]
    private ?int $percentage = null;

    #[ORM\Column(nullable: true)]
    #[Assert\When(
        expression: 'this.isIsDeal() === true',
        constraints: [
            new Assert\notNull(),
            new Assert\Positive()
        ]
    )]
    #[Assert\When(
        expression: 'this.isIsDeal() === false',
        constraints: [
            new Assert\IsNull()
        ]
    )]
    #[Groups(['product:read', 'product:create'])]
    private ?float $priceDeal = null;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\When(
        expression: 'this.isIsArchive() === true',
        constraints: [
            new Assert\IsFalse()
        ]
    )]
    #[Groups(['product:read', 'product:create'])]
    private ?bool $isDeal = null;
    
    #[ORM\Column]
    #[Assert\NotNull]
    #[Groups(['product:read', 'product:create'])]
    private ?bool $isArchive = null;

    #[ORM\Column]
    #[Groups(['product:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['product:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct() {
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->Price;
    }

    public function setPrice(?float $Price): self
    {
        $this->Price = $Price;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getFinishDealAt(): ?\DateTimeImmutable
    {
        return $this->finishDealAt;
    }

    public function setFinishDealAt(?\DateTimeImmutable $finishAt): self
    {
        $this->finishDealAt = $finishAt;

        return $this;
    }

    public function getPercentage(): ?int
    {
        return $this->percentage;
    }

    public function setPercentage(?int $percentage): self
    {
        $this->percentage = $percentage;

        return $this;
    }

    public function getPriceDeal(): ?float
    {
        return $this->priceDeal;
    }

    public function setPriceDeal(?float $priceDeal): self
    {
        $this->priceDeal = $priceDeal;

        return $this;
    }

    public function isIsDeal(): ?bool
    {
        return $this->isDeal;
    }

    public function setIsDeal(bool $isDeal): self
    {
        $this->isDeal = $isDeal;

        return $this;
    }

    public function isIsArchive(): ?bool
    {
        return $this->isArchive;
    }

    public function setIsArchive(bool $isArchive): self
    {
        $this->isArchive = $isArchive;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }


}
