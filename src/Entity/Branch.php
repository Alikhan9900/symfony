<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Branch
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\Column(type:"string", length:150)]
    #[Assert\NotBlank(message: "Branch name cannot be empty.")]
    #[Assert\Length(
        min: 2,
        max: 150,
        minMessage: "Branch name must be at least {{ limit }} characters long.",
        maxMessage: "Branch name cannot be longer than {{ limit }} characters."
    )]
    #[Assert\Regex(
        pattern: '/^[\p{L}0-9\s\-,.]+$/u',
        message: "Branch name can contain letters, numbers, spaces, commas, hyphens and dots."
    )]
    private string $name;

    #[ORM\ManyToOne(targetEntity: Address::class, cascade: ["persist"])]
    #[Assert\Valid]
    private ?Address $address = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): self
    {
        $this->address = $address;
        return $this;
    }
}
