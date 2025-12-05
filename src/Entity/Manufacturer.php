<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\ManufacturerRepository;

#[ORM\Entity(repositoryClass: ManufacturerRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            security: "is_granted('ROLE_USER')",
            normalizationContext: ['groups' => ['manufacturer_read']]
        ),

        new GetCollection(
            security: "is_granted('ROLE_USER')",
            normalizationContext: ['groups' => ['manufacturer_read']]
        ),

        new Post(
            security: "is_granted('ROLE_ADMIN')",
            denormalizationContext: ['groups' => ['manufacturer_write']],
            normalizationContext: ['groups' => ['manufacturer_read']]
        ),

        new Patch(
            security: "is_granted('ROLE_ADMIN')",
            denormalizationContext: ['groups' => ['manufacturer_write']],
            normalizationContext: ['groups' => ['manufacturer_read']]
        ),

        new Delete(
            security: "is_granted('ROLE_ADMIN')"
        )
    ],
    paginationItemsPerPage: 10,
    normalizationContext: ['groups' => ['manufacturer_read']],
    denormalizationContext: ['groups' => ['manufacturer_write']]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'name' => 'partial'
])]
#[ApiFilter(OrderFilter::class, properties: ['id', 'name'], arguments: ['orderParameterName' => 'order'])]
class Manufacturer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:"integer")]
    #[Groups(['manufacturer_read'])]
    private ?int $id = null;

    #[ORM\Column(type:"string", length:150)]
    #[Groups(['manufacturer_read', 'manufacturer_write'])]
    #[Assert\NotBlank(message: "Manufacturer name cannot be empty.")]
    #[Assert\Length(
        min: 2,
        max: 150,
        minMessage: "Manufacturer name must be at least {{ limit }} characters long.",
        maxMessage: "Manufacturer name cannot exceed {{ limit }} characters."
    )]
    #[Assert\Regex(
        pattern: '/^[\p{L}0-9\s\-,.]+$/u',
        message: "Manufacturer name may contain letters, numbers, spaces, commas, hyphens, and dots."
    )]
    private string $name;

    public function getId(): ?int { return $this->id; }

    public function getName(): string { return $this->name; }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }
}
