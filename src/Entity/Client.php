<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\Column(type:"string", length:100)]
    #[Assert\NotBlank(message: "First name cannot be empty.")]
    #[Assert\Length(min: 2, max: 100)]
    #[Assert\Regex(
        pattern: '/^[\p{L}\s\-]+$/u',
        message: "First name can contain only letters, spaces and hyphens."
    )]
    private string $firstName;

    #[ORM\Column(type:"string", length:100)]
    #[Assert\NotBlank(message: "Last name cannot be empty.")]
    #[Assert\Length(min: 2, max: 100)]
    #[Assert\Regex(
        pattern: '/^[\p{L}\s\-]+$/u',
        message: "Last name can contain only letters, spaces and hyphens."
    )]
    private string $lastName;

    #[ORM\Column(type:"string", length:150, nullable:true)]
    #[Assert\Email(message: "Invalid email format.")]
    private ?string $email = null;

    #[ORM\Column(type:"string", length:50, nullable:true)]
    #[Assert\Regex(
        pattern: '/^\+?[0-9\-\s]{7,50}$/',
        message: "Phone number may contain only digits, spaces, hyphens and optional '+' sign."
    )]
    private ?string $phone = null;

    #[ORM\ManyToOne(targetEntity: Address::class, cascade: ["persist"])]
    #[Assert\Valid]
    private ?Address $address = null;

    #[ORM\Column(type:"string", length:100, nullable:true)]
    #[Assert\Length(min: 3, max: 100)]
    #[Assert\Regex(
        pattern: '/^[A-Za-z0-9\-]+$/',
        message: "Driver license may contain only letters, digits and hyphens."
    )]
    private ?string $driverLicense = null;

    public function getId(): ?int { return $this->id; }

    public function getFirstName(): string { return $this->firstName; }
    public function setFirstName(string $n): self { $this->firstName = $n; return $this; }

    public function getLastName(): string { return $this->lastName; }
    public function setLastName(string $n): self { $this->lastName = $n; return $this; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(?string $e): self { $this->email = $e; return $this; }

    public function getPhone(): ?string { return $this->phone; }
    public function setPhone(?string $p): self { $this->phone = $p; return $this; }

    public function getAddress(): ?Address { return $this->address; }
    public function setAddress(?Address $a): self { $this->address = $a; return $this; }

    public function getDriverLicense(): ?string { return $this->driverLicense; }
    public function setDriverLicense(?string $d): self { $this->driverLicense = $d; return $this; }
}
