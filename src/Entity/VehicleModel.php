<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class VehicleModel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Manufacturer::class)]
    #[ORM\JoinColumn(nullable:false, onDelete:"CASCADE")]
    #[Assert\NotNull(message: "Manufacturer must be specified.")]
    #[Assert\Valid]
    private Manufacturer $manufacturer;

    #[ORM\Column(type:"string", length:150)]
    #[Assert\NotBlank(message: "Model name cannot be empty.")]
    #[Assert\Length(min: 2, max: 150)]
    #[Assert\Regex(
        pattern: '/^[\p{L}0-9\s\-,.]+$/u',
        message: "Model name can contain letters, numbers, spaces, hyphens, commas and dots."
    )]
    private string $name;

    #[ORM\Column(type:"smallint", nullable:true)]
    #[Assert\Range(
        min: 1,
        max: 20,
        notInRangeMessage: "Seats must be between {{ min }} and {{ max }}."
    )]
    private ?int $seats = null;


    public function getId(): ?int { return $this->id; }
    public function getManufacturer(): Manufacturer { return $this->manufacturer; }
    public function setManufacturer(Manufacturer $m): self { $this->manufacturer = $m; return $this; }
    public function getName(): string { return $this->name; }
    public function setName(string $n): self { $this->name = $n; return $this; }
    public function getSeats(): ?int { return $this->seats; }
    public function setSeats(?int $s): self { $this->seats = $s; return $this; }
}
