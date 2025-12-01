<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Branch
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\Column(type:"string", length:150)]
    private string $name;

    #[ORM\ManyToOne(targetEntity: Address::class, cascade:["persist"])]
    private ?Address $address = null;

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function setName(string $n): self { $this->name = $n; return $this; }
    public function getAddress(): ?Address { return $this->address; }
    public function setAddress(?Address $a): self { $this->address = $a; return $this; }
}
