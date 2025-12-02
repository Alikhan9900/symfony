<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Vehicle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: VehicleModel::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "Vehicle model must be provided.")]
    #[Assert\Valid]
    private VehicleModel $model;

    #[ORM\Column(type:"string", length:50, unique:true)]
    #[Assert\NotBlank(message: "VIN cannot be empty.")]
    #[Assert\Length(
        min: 3,
        max: 50,
        minMessage: "VIN must be at least {{ limit }} characters long.",
        maxMessage: "VIN cannot exceed {{ limit }} characters."
    )]
    #[Assert\Regex(
        pattern: '/^[A-Za-z0-9\-]+$/',
        message: "VIN may contain only letters, digits, and hyphens."
    )]
    private string $vin;

    #[ORM\Column(type:"smallint", nullable:true)]
    #[Assert\Range(
        min: 1886,
        max: 2100,
        notInRangeMessage: "Year must be between {{ min }} and {{ max }}."
    )]
    private ?int $year = null;


    #[ORM\Column(type:"integer", options:["default"=>0])]
    #[Assert\NotNull]
    #[Assert\PositiveOrZero(message: "Mileage cannot be negative.")]
    private int $mileage = 0;

    #[ORM\Column(type:"string", length:50)]
    #[Assert\NotBlank]
    #[Assert\Choice(
        choices: ["available", "rented", "maintenance"],
        message: "Invalid status. Allowed values: available, rented, maintenance."
    )]
    private string $status = 'available';

    #[ORM\ManyToOne(targetEntity: Branch::class)]
    #[Assert\Valid]
    private ?Branch $branch = null;

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
