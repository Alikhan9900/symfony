<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class MaintenanceRecord
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Vehicle::class)]
    #[ORM\JoinColumn(nullable:false)]
    #[Assert\NotNull(message: "Vehicle must be specified.")]
    #[Assert\Valid]
    private Vehicle $vehicle;

    #[ORM\Column(type:"datetime", nullable:true)]
    #[Assert\Type(\DateTimeInterface::class, message: "performedAt must be a valid datetime.")]
    private ?\DateTimeInterface $performedAt = null;

    #[ORM\Column(type:"text", nullable:true)]
    #[Assert\Length(
        max: 5000,
        maxMessage: "Description cannot exceed {{ limit }} characters."
    )]
    private ?string $description = null;

    #[ORM\Column(type:"decimal", precision:10, scale:2, nullable:true)]
    #[Assert\Regex(
        pattern: '/^\d{1,8}(\.\d{1,2})?$/',
        message: "Cost must be a valid decimal number with up to 2 decimal places."
    )]
    #[Assert\PositiveOrZero(message: "Cost cannot be negative.")]
    private ?string $cost = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVehicle(): Vehicle
    {
        return $this->vehicle;
    }

    public function setVehicle(Vehicle $v): self
    {
        $this->vehicle = $v;
        return $this;
    }

    public function getPerformedAt(): ?\DateTimeInterface
    {
        return $this->performedAt;
    }

    public function setPerformedAt(?\DateTimeInterface $d): self
    {
        $this->performedAt = $d;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $s): self
    {
        $this->description = $s;
        return $this;
    }

    public function getCost(): ?string
    {
        return $this->cost;
    }

    public function setCost(?string $c): self
    {
        $this->cost = $c;
        return $this;
    }
}
