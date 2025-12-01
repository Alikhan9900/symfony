<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Address
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\Column(type:"string", length:100, nullable:true)]
    private ?string $country = null;

    #[ORM\Column(type:"string", length:100, nullable:true)]
    private ?string $city = null;

    #[ORM\Column(type:"string", length:255, nullable:true)]
    private ?string $street = null;

    #[ORM\Column(type:"string", length:20, nullable:true)]
    private ?string $zip = null;

    public function getId(): ?int { return $this->id; }
    public function getCountry(): ?string { return $this->country; }
    public function setCountry(?string $c): self { $this->country = $c; return $this; }
    public function getCity(): ?string { return $this->city; }
    public function setCity(?string $c): self { $this->city = $c; return $this; }
    public function getStreet(): ?string { return $this->street; }
    public function setStreet(?string $s): self { $this->street = $s; return $this; }
    public function getZip(): ?string { return $this->zip; }
    public function setZip(?string $z): self { $this->zip = $z; return $this; }
}
