<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Payment
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Rental::class)]
    #[ORM\JoinColumn(nullable:false)]
    private Rental $rental;

    #[ORM\Column(type:"datetime", nullable:true)]
    private ?\DateTimeInterface $paidAt = null;

    #[ORM\Column(type:"decimal", precision:10, scale:2)]
    private string $amount;

    #[ORM\Column(type:"string", length:50, nullable:true)]
    private ?string $method = null;

    #[ORM\Column(type:"string", length:255, nullable:true)]
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
