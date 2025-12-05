<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;

use App\Repository\VehicleModelRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: VehicleModelRepository::class)]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_USER')"),
        new GetCollection(security: "is_granted('ROLE_USER')"),
        new Post(security: "is_granted('ROLE_ADMIN')"),
        new Patch(security: "is_granted('ROLE_ADMIN')"),
        new Delete(security: "is_granted('ROLE_ADMIN')")
    ],
    normalizationContext: ['groups' => ['vehicle_model_read']],
    denormalizationContext: ['groups' => ['vehicle_model_write']]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'name' => 'partial',
    'manufacturer.id' => 'exact',
])]
#[ApiFilter(OrderFilter::class, properties: [
    'id', 'name', 'seats'
], arguments: ['orderParameterName' => 'order'])]
class VehicleModel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:"integer")]
    #[Groups(['vehicle_model_read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Manufacturer::class)]
    #[ORM\JoinColumn(nullable:false, onDelete:"CASCADE")]
    #[Assert\NotNull(message: "Manufacturer must be specified.")]
    #[Assert\Valid]
    #[Groups(['vehicle_model_read', 'vehicle_model_write'])]
    private Manufacturer $manufacturer;

    #[ORM\Column(type:"string", length:150)]
    #[Assert\NotBlank(message: "Model name cannot be empty.")]
    #[Assert\Length(min: 2, max: 150)]
    #[Assert\Regex(
        pattern: '/^[\p{L}0-9\s\-,.]+$/u',
        message: "Model name can contain letters, numbers, spaces, hyphens, commas and dots."
    )]
    #[Groups(['vehicle_model_read', 'vehicle_model_write'])]
    private string $name;

    #[ORM\Column(type:"smallint", nullable:true)]
    #[Assert\Range(
        min: 1,
        max: 20,
        notInRangeMessage: "Seats must be between {{ min }} and {{ max }}."
    )]
    #[Groups(['vehicle_model_read', 'vehicle_model_write'])]
    private ?int $seats = null;

    public function getId(): ?int { return $this->id; }

    public function getManufacturer(): Manufacturer { return $this->manufacturer; }
    public function setManufacturer(Manufacturer $m): self { $this->manufacturer = $m; return $this; }

    public function getName(): string { return $this->name; }
    public function setName(string $n): self { $this->name = $n; return $this; }

    public function getSeats(): ?int { return $this->seats; }
    public function setSeats(?int $s): self { $this->seats = $s; return $this; }
}
