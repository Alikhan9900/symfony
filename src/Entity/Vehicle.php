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

use App\Repository\VehicleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: VehicleRepository::class)]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_USER')"),
        new GetCollection(security: "is_granted('ROLE_USER')"),
        new Post(security: "is_granted('ROLE_ADMIN')"),
        new Patch(security: "is_granted('ROLE_ADMIN')"),
        new Delete(security: "is_granted('ROLE_ADMIN')")
    ],
    normalizationContext: ['groups' => ['vehicle_read']],
    denormalizationContext: ['groups' => ['vehicle_write']]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'vin' => 'partial',
    'status' => 'partial',
    'model.id' => 'exact',
    'branch.id' => 'exact'
])]
#[ApiFilter(OrderFilter::class, properties: [
    'id', 'year', 'mileage', 'status'
], arguments: ['orderParameterName' => 'order'])]
class Vehicle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:"integer")]
    #[Groups(['vehicle_read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: VehicleModel::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "Vehicle model must be provided.")]
    #[Assert\Valid]
    #[Groups(['vehicle_read', 'vehicle_write'])]
    private VehicleModel $model;

    #[ORM\Column(type:"string", length:50, unique:true)]
    #[Assert\NotBlank(message: "VIN cannot be empty.")]
    #[Assert\Length(min: 3, max: 50)]
    #[Assert\Regex(
        pattern: '/^[A-Za-z0-9\-]+$/',
        message: "VIN may contain only letters, digits, and hyphens."
    )]
    #[Groups(['vehicle_read', 'vehicle_write'])]
    private string $vin;

    #[ORM\Column(type:"smallint", nullable:true)]
    #[Assert\Range(min: 1886, max: 2100)]
    #[Groups(['vehicle_read', 'vehicle_write'])]
    private ?int $year = null;

    #[ORM\Column(type:"integer", options:["default" => 0])]
    #[Assert\NotNull]
    #[Assert\PositiveOrZero(message: "Mileage cannot be negative.")]
    #[Groups(['vehicle_read', 'vehicle_write'])]
    private int $mileage = 0;

    #[ORM\Column(type:"string", length:50)]
    #[Assert\NotBlank]
    #[Assert\Choice(
        choices: ["available", "rented", "maintenance"],
        message: "Invalid status. Allowed values: available, rented, maintenance."
    )]
    #[Groups(['vehicle_read', 'vehicle_write'])]
    private string $status = 'available';

    #[ORM\ManyToOne(targetEntity: Branch::class)]
    #[Assert\Valid]
    #[Groups(['vehicle_read', 'vehicle_write'])]
    private ?Branch $branch = null;

    // Getters / Setters
    public function getId(): ?int { return $this->id; }

    public function getModel(): VehicleModel { return $this->model; }
    public function setModel(VehicleModel $m): self { $this->model = $m; return $this; }

    public function getVin(): string { return $this->vin; }
    public function setVin(string $v): self { $this->vin = $v; return $this; }

    public function getYear(): ?int { return $this->year; }
    public function setYear(?int $y): self { $this->year = $y; return $this; }

    public function getMileage(): int { return $this->mileage; }
    public function setMileage(int $m): self { $this->mileage = $m; return $this; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $s): self { $this->status = $s; return $this; }

    public function getBranch(): ?Branch { return $this->branch; }
    public function setBranch(?Branch $b): self { $this->branch = $b; return $this; }
}
