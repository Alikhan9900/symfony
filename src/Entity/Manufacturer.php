<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Manufacturer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\Column(type:"string", length:150)]
    #[Assert\NotBlank(message: "Manufacturer name cannot be empty.")]
    #[Assert\Length(
        min: 2,
        max: 150,
        minMessage: "Manufacturer name must be at least {{ limit }} characters long.",
        maxMessage: "Manufacturer name cannot exceed {{ limit }} characters."
    )]
    #[Assert\Regex(
        pattern: '/^[\p{L}0-9\s\-,.]+$/u',
        message: "Manufacturer name may contain letters, numbers, spaces, commas, hyphens and dots."
    )]
    private string $name;

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
}
