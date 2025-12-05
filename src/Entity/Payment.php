<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;

use App\Repository\PaymentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_USER')"),
        new GetCollection(security: "is_granted('ROLE_USER')"),
        new Post(security: "is_granted('ROLE_ADMIN')"),
        new Patch(security: "is_granted('ROLE_ADMIN')"),
        new Delete(security: "is_granted('ROLE_ADMIN')")
    ],
    normalizationContext: ['groups' => ['payment_read']],
    denormalizationContext: ['groups' => ['payment_write']]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'rental.id' => 'exact',
    'method' => 'partial',
    'transactionId' => 'partial'
])]
#[ApiFilter(OrderFilter::class, properties: [
    'id', 'paidAt', 'amount'
], arguments: ['orderParameterName' => 'order'])]
class Payment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:"integer")]
    #[Groups(['payment_read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Rental::class)]
    #[ORM\JoinColumn(nullable:false)]
    #[Assert\NotNull(message: "Rental must be specified.")]
    #[Assert\Valid]
    #[Groups(['payment_read', 'payment_write'])]
    private Rental $rental;

    #[ORM\Column(type:"datetime", nullable:true)]
    #[Assert\Type(\DateTimeInterface::class, message: "paidAt must be a valid datetime.")]
    #[Groups(['payment_read', 'payment_write'])]
    private ?\DateTimeInterface $paidAt = null;

    #[ORM\Column(type:"decimal", precision:10, scale:2)]
    #[Assert\NotBlank(message: "Amount cannot be empty.")]
    #[Assert\Regex(
        pattern: '/^\d{1,8}(\.\d{1,2})?$/',
        message: "Amount must be a valid decimal number with up to 2 decimal places."
    )]
    #[Assert\PositiveOrZero(message: "Amount cannot be negative.")]
    #[Groups(['payment_read', 'payment_write'])]
    private string $amount;

    #[ORM\Column(type:"string", length:50, nullable:true)]
    #[Assert\Length(max: 50)]
    #[Assert\Choice(
        choices: ["cash", "card", "online", "bank_transfer"],
        message: "Invalid payment method."
    )]
    #[Groups(['payment_read', 'payment_write'])]
    private ?string $method = null;

    #[ORM\Column(type:"string", length:255, nullable:true)]
    #[Assert\Length(max: 255)]
    #[Assert\Regex(
        pattern: '/^[A-Za-z0-9\-]+$/',
        message: "Transaction ID may contain only letters, digits and hyphens."
    )]
    #[Groups(['payment_read', 'payment_write'])]
    private ?string $transactionId = null;

    public function getId(): ?int { return $this->id; }
    public function getRental(): Rental { return $this->rental; }
    public function setRental(Rental $r): self { $this->rental = $r; return $this; }
    public function getPaidAt(): ?\DateTimeInterface { return $this->paidAt; }
    public function setPaidAt(?\DateTimeInterface $d): self { $this->paidAt = $d; return $this; }
    public function getAmount(): string { return $this->amount; }
    public function setAmount(string $a): self { $this->amount = $a; return $this; }
    public function getMethod(): ?string { return $this->method; }
    public function setMethod(?string $m): self { $this->method = $m; return $this; }
    public function getTransactionId(): ?string { return $this->transactionId; }
    public function setTransactionId(?string $t): self { $this->transactionId = $t; return $this; }
}
