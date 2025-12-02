<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Employee
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

    #[ORM\ManyToOne(targetEntity: Branch::class)]
    #[Assert\Valid]
    private ?Branch $branch = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $n): self
    {
        $this->firstName = $n;
        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $n): self
    {
        $this->lastName = $n;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $e): self
    {
        $this->email = $e;
        return $this;
    }

    public function getBranch(): ?Branch
    {
        return $this->branch;
    }

    public function setBranch(?Branch $b): self
    {
        $this->branch = $b;
        return $this;
    }
}
