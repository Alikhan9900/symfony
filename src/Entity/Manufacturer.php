<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['manufacturer:read']]
        ),
        new Post(
            denormalizationContext: ['groups' => ['manufacturer:write']],
            normalizationContext: ['groups' => ['manufacturer:read']]
        ),
        new Get(
            normalizationContext: ['groups' => ['manufacturer:read']]
        ),
        new Patch(
            denormalizationContext: ['groups' => ['manufacturer:write']],
            normalizationContext: ['groups' => ['manufacturer:read']]
        ),
        new Delete()
    ],
    normalizationContext: ['groups' => ['manufacturer:read']],
    denormalizationContext: ['groups' => ['manufacturer:write']]
)]
class Manufacturer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:"integer")]
    #[Groups(['manufacturer:read'])]
    private ?int $id = null;

    #[ORM\Column(type:"string", length:150)]
    #[Groups(['manufacturer:read', 'manufacturer:write'])]
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
