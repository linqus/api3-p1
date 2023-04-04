<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Serializer\Filter\PropertyFilter;
use App\Repository\DragonTreasureRepository;
use Carbon\Carbon;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

use function Symfony\Component\String\u;

#[ORM\Entity(repositoryClass: DragonTreasureRepository::class)]
#[ApiResource(
    description: 'A rare and valuable treasure.',
    shortName: 'Treasures',
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Patch(),
        new Put(),
    ],
    normalizationContext: [
        'groups' => ['treasure:read'],
    ],
    denormalizationContext: [
        'groups' => ['treasure:write']
    ],
    paginationItemsPerPage: 10,
    formats: [
        'jsonld',
        'json',
        'html',
        //'csv' => 'text/csv',
        'jsonhal' => 'application/hal+json',
    ],
)]
// #[ApiFilter(BooleanFilter::class, properties:['isPublished'])]
#[ApiFilter(PropertyFilter::class)]
class DragonTreasure
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['treasure:read', 'treasure:write'])]
    #[ApiFilter(SearchFilter::class, strategy: SearchFilter::STRATEGY_PARTIAL)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 2, max: 50, maxMessage: 'Describe treasure in 50 characters or less')]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups('treasure:read')]
    #[ApiFilter(SearchFilter::class, strategy: SearchFilter::STRATEGY_PARTIAL)]
    #[Assert\NotBlank()]
    private ?string $description = null;

    /**
     * Estimated value of a treasure in gold coins
     */
    #[ORM\Column]
    #[Groups(['treasure:read', 'treasure:write'])]
    #[ApiFilter(RangeFilter::class)]
    #[Assert\GreaterThanOrEqual(0)]
    private ?int $value = 0;

    #[ORM\Column]
    #[Groups('treasure:read')]
    #[ApiFilter(RangeFilter::class)]
    #[Assert\GreaterThanOrEqual(0)]
    #[Assert\LessThanOrEqual(10)]
    private ?int $coolFactor = 0;

    #[ORM\Column]
    private ?\DateTimeImmutable $plunderedAt;

    #[ORM\Column]
    #[ApiFilter(BooleanFilter::class)]
    private bool $isPublished = false;

    #[ORM\ManyToOne(inversedBy: 'dragonTreasures')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['treasure:read', 'treasure:write'])]
    private ?User $ownedBy = null;

    public function __construct(string $name = null)
    {
        $this->name = $name;
        $this->plunderedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    #[Groups('treasure:read')]
    public function getShortDescription(): ?string
    {
        return u($this->description)->truncate(40,'...');
    }
    

    public function getDescription(): ?string
    {
        return $this->description;
    }
    
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }
 
    #[Groups('treasure:write')]
    #[SerializedName('description')]
    public function setTextDescription(string $description): self
    {
        $this->description = nl2br($description);

        return $this;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getCoolFactor(): ?int
    {
        return $this->coolFactor;
    }
    #[Groups(['treasure:write'])]
    public function setCoolFactor(int $coolFactor): self
    {
        $this->coolFactor = $coolFactor;

        return $this;
    }

    public function getPlunderedAt(): ?\DateTimeImmutable
    {
        return $this->plunderedAt;
    }
    
    public function setPlunderedAt(?\DateTimeImmutable $plunderedAt): self
    {
        $this->plunderedAt = $plunderedAt;

        return $this;
    }

    /**
     * Human readable representation of when the treasure was plundered.
     */
     #[Groups(['treasure:read'])]
     public function getPlunderedAgo(): ?string
    {
        return Carbon::instance($this->plunderedAt)->diffForHumans();
    }

    public function getIsPublished(): bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public function getOwnedBy(): ?User
    {
        return $this->ownedBy;
    }

    public function setOwnedBy(?User $ownedBy): self
    {
        $this->ownedBy = $ownedBy;

        return $this;
    }
}
