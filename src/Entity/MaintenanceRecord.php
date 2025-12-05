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

use App\Repository\MaintenanceRecordRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: MaintenanceRecordRepository::class)]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_USER')"),
        new GetCollection(security: "is_granted('ROLE_USER')"),
        new Post(security: "is_granted('ROLE_ADMIN')"),
        new Patch(security: "is_granted('ROLE_ADMIN')"),
        new Delete(security: "is_granted('ROLE_ADMIN')")
    ],
    normalizationContext: ['groups' => ['maintenance_read']],
    denormalizationContext: ['groups' => ['maintenance_write']]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'vehicle.id' => 'exact',
    'vehicle.vin' => 'partial',
    'description' => 'partial'
])]
#[ApiFilter(OrderFilter::class, properties: [
    'id', 'performedAt', 'cost'
], arguments: ['orderParameterName' => 'order'])]
class MaintenanceRecord
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:"integer")]
    #[Groups(['maintenance_read', 'vehicle_read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Vehicle::class)]
    #[ORM\JoinColumn(nullable:false)]
    #[Assert\NotNull(message: "Vehicle must be specified.")]
    #[Assert\Valid]
    #[Groups(['maintenance_read', 'maintenance_write'])]
    private Vehicle $vehicle;

    #[ORM\Column(type:"datetime", nullable:true)]
    #[Assert\Type(\DateTimeInterface::class, message: "performedAt must be a valid datetime.")]
    #[Groups(['maintenance_read', 'maintenance_write'])]
    private ?\DateTimeInterface $performedAt = null;

    #[ORM\Column(type:"text", nullable:true)]
    #[Assert\Length(max: 5000)]
    #[Groups(['maintenance_read', 'maintenance_write'])]
    private ?string $description = null;

    #[ORM\Column(type:"decimal", precision:10, scale:2, nullable:true)]
    #[Assert\Regex(
        pattern: '/^\d{1,8}(\.\d{1,2})?$/',
        message: "Cost must be a valid decimal number with up to 2 decimal places."
    )]
    #[Assert\PositiveOrZero(message: "Cost cannot be negative.")]
    #[Groups(['maintenance_read', 'maintenance_write'])]
    private ?string $cost = null;

    public function getId(): ?int { return $this->id; }

    public function getVehicle(): Vehicle { return $this->vehicle; }
    public function setVehicle(Vehicle $v): self { $this->vehicle = $v; return $this; }

    public function getPerformedAt(): ?\DateTimeInterface { return $this->performedAt; }
    public function setPerformedAt(?\DateTimeInterface $d): self { $this->performedAt = $d; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $s): self { $this->description = $s; return $this; }

    public function getCost(): ?string { return $this->cost; }
    public function setCost(?string $c): self { $this->cost = $c; return $this; }
}
