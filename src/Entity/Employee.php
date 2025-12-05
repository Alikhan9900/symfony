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

use App\Repository\EmployeeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: EmployeeRepository::class)]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_USER')"),
        new GetCollection(security: "is_granted('ROLE_USER')"),
        new Post(security: "is_granted('ROLE_ADMIN')"),
        new Patch(security: "is_granted('ROLE_ADMIN')"),
        new Delete(security: "is_granted('ROLE_ADMIN')")
    ],
    normalizationContext: ['groups' => ['employee_read']],
    denormalizationContext: ['groups' => ['employee_write']]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'firstName' => 'partial',
    'lastName' => 'partial',
    'email' => 'partial',
    'branch.name' => 'partial'
])]
#[ApiFilter(OrderFilter::class, properties: ['id', 'firstName', 'lastName'], arguments: ['orderParameterName' => 'order'])]
class Employee
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:"integer")]
    #[Groups(['employee_read', 'branch_read'])]
    private ?int $id = null;

    #[ORM\Column(type:"string", length:100)]
    #[Assert\NotBlank(message: "First name cannot be empty.")]
    #[Assert\Length(min: 2, max: 100)]
    #[Assert\Regex(
        pattern: '/^[\p{L}\s\-]+$/u',
        message: "First name can contain only letters, spaces and hyphens."
    )]
    #[Groups(['employee_read', 'employee_write', 'branch_read'])]
    private string $firstName;

    #[ORM\Column(type:"string", length:100)]
    #[Assert\NotBlank(message: "Last name cannot be empty.")]
    #[Assert\Length(min: 2, max: 100)]
    #[Assert\Regex(
        pattern: '/^[\p{L}\s\-]+$/u',
        message: "Last name can contain only letters, spaces and hyphens."
    )]
    #[Groups(['employee_read', 'employee_write', 'branch_read'])]
    private string $lastName;

    #[ORM\Column(type:"string", length:150, nullable:true)]
    #[Assert\Email(message: "Invalid email format.")]
    #[Groups(['employee_read', 'employee_write'])]
    private ?string $email = null;

    #[ORM\ManyToOne(targetEntity: Branch::class)]
    #[Assert\Valid]
    #[Groups(['employee_read', 'employee_write'])]
    private ?Branch $branch = null;

    public function getId(): ?int { return $this->id; }

    public function getFirstName(): string { return $this->firstName; }
    public function setFirstName(string $n): self { $this->firstName = $n; return $this; }

    public function getLastName(): string { return $this->lastName; }
    public function setLastName(string $n): self { $this->lastName = $n; return $this; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(?string $e): self { $this->email = $e; return $this; }

    public function getBranch(): ?Branch { return $this->branch; }
    public function setBranch(?Branch $b): self { $this->branch = $b; return $this; }
}
