<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class VehicleModel
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Manufacturer::class)]
    #[ORM\JoinColumn(nullable:false, onDelete:"CASCADE")]
    private Manufacturer $manufacturer;

    #[ORM\Column(type:"string", length:150)]
    private string $name;

    #[ORM\Column(type:"smallint", nullable:true)]
    private ?int $seats = null;

    public function getId(): ?int { return $this->id; }
    public function getManufacturer(): Manufacturer { return $this->manufacturer; }
    public function setManufacturer(Manufacturer $m): self { $this->manufacturer = $m; return $this; }
    public function getName(): string { return $this->name; }
    public function setName(string $n): self { $this->name = $n; return $this; }
    public function getSeats(): ?int { return $this->seats; }
    public function setSeats(?int $s): self { $this->seats = $s; return $this; }
}
