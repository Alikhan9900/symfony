<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;

use App\Repository\BranchRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BranchRepository::class)]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_USER')"),
        new GetCollection(security: "is_granted('ROLE_USER')"),
        new Post(security: "is_granted('ROLE_ADMIN')"),
        new Patch(security: "is_granted('ROLE_ADMIN')"),
        new Delete(security: "is_granted('ROLE_ADMIN')")
    ],
    normalizationContext: ['groups' => ['branch_read']],
    denormalizationContext: ['groups' => ['branch_write']]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'name' => 'partial',
    'address.city' => 'partial',
    'address.country' => 'partial'
])]
#[ApiFilter(OrderFilter::class, properties: ['id', 'name'], arguments: ['orderParameterName' => 'order'])]
class Branch
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:"integer")]
    #[Groups(['branch_read', 'client_read', 'employee_read'])]
    private ?int $id = null;

    #[ORM\Column(type:"string", length:150)]
    #[Assert\NotBlank(message: "Branch name cannot be empty.")]
    #[Assert\Length(min: 2, max: 150)]
    #[Assert\Regex(
        pattern: '/^[\p{L}0-9\s\-,.]+$/u',
        message: "Branch name can contain letters, numbers, spaces, commas, hyphens and dots."
    )]
    #[Groups(['branch_read', 'branch_write', 'client_read', 'employee_read'])]
    private string $name;

    #[ORM\ManyToOne(targetEntity: Address::class, cascade: ["persist"])]
    #[Assert\Valid]
    #[Groups(['branch_read', 'branch_write'])]
    private ?Address $address = null;

    public function getId(): ?int { return $this->id; }

    public function getName(): string { return $this->name; }
    public function setName(string $name): self { $this->name = $name; return $this; }

    public function getAddress(): ?Address { return $this->address; }
    public function setAddress(?Address $address): self { $this->address = $address; return $this; }
}
