<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
class Address
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    #[Groups([
        'client_read', 'client_write',
        'branch_read', 'branch_write',
        'employee_read', 'employee_write'
    ])]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 100, nullable: true)]
    #[Assert\Length(max: 100)]
    #[Assert\Regex(
        pattern: '/^[\p{L}\s\-]+$/u',
        message: 'Country can contain only letters, spaces and hyphens.'
    )]
    #[Groups([
        'client_read', 'client_write',
        'branch_read', 'branch_write',
        'employee_read', 'employee_write'
    ])]
    private ?string $country = null;

    #[ORM\Column(type: "string", length: 100, nullable: true)]
    #[Assert\Length(max: 100)]
    #[Assert\Regex(
        pattern: '/^[\p{L}\s\-]+$/u',
        message: 'City can contain only letters, spaces and hyphens.'
    )]
    #[Groups([
        'client_read', 'client_write',
        'branch_read', 'branch_write',
        'employee_read', 'employee_write'
    ])]
    private ?string $city = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Assert\Length(max: 255)]
    #[Assert\Regex(
        pattern: '/^[\p{L}0-9\s\-,.]+$/u',
        message: 'Street may contain letters, numbers, spaces, commas and dots.'
    )]
    #[Groups([
        'client_read', 'client_write',
        'branch_read', 'branch_write',
        'employee_read', 'employee_write'
    ])]
    private ?string $street = null;

    #[ORM\Column(type: "string", length: 20, nullable: true)]
    #[Assert\Length(max: 20)]
    #[Assert\Regex(
        pattern: '/^[A-Za-z0-9\-]+$/',
        message: 'ZIP code may include digits, letters and hyphens only.'
    )]
    #[Groups([
        'client_read', 'client_write',
        'branch_read', 'branch_write',
        'employee_read', 'employee_write'
    ])]
    private ?string $zip = null;

    public function getId(): ?int { return $this->id; }
    public function getCountry(): ?string { return $this->country; }
    public function setCountry(?string $country): self { $this->country = $country; return $this; }

    public function getCity(): ?string { return $this->city; }
    public function setCity(?string $city): self { $this->city = $city; return $this; }

    public function getStreet(): ?string { return $this->street; }
    public function setStreet(?string $street): self { $this->street = $street; return $this; }

    public function getZip(): ?string { return $this->zip; }
    public function setZip(?string $zip): self { $this->zip = $zip; return $this; }
}
