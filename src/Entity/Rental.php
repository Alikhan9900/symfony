<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Rental
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Client::class)]
    #[ORM\JoinColumn(nullable:false)]
    #[Assert\NotNull(message: "Client must be specified.")]
    #[Assert\Valid]
    private Client $client;

    #[ORM\ManyToOne(targetEntity: Vehicle::class)]
    #[ORM\JoinColumn(nullable:false)]
    #[Assert\NotNull(message: "Vehicle must be specified.")]
    #[Assert\Valid]
    private Vehicle $vehicle;

    #[ORM\ManyToOne(targetEntity: Employee::class)]
    #[Assert\Valid]
    private ?Employee $employee = null;

    #[ORM\Column(type:"datetime", nullable:true)]
    #[Assert\Type(\DateTimeInterface::class)]
    private ?\DateTimeInterface $startDatetime = null;

    #[ORM\Column(type:"datetime", nullable:true)]
    #[Assert\Type(\DateTimeInterface::class)]
    private ?\DateTimeInterface $endDatetime = null;

    #[ORM\Column(type:"decimal", precision:10, scale:2, nullable:true)]
    #[Assert\Regex(
        pattern: '/^\d{1,8}(\.\d{1,2})?$/',
        message: "Total amount must be a valid decimal number."
    )]
    #[Assert\PositiveOrZero(message: "Total amount cannot be negative.")]
    private ?string $totalAmount = null;

    #[ORM\Column(type:"string", length:50)]
    #[Assert\NotBlank]
    #[Assert\Choice(
        choices: ["new", "active", "completed", "canceled"],
        message: "Invalid rental status."
    )]
    private string $status = 'new';

    public function getId(): ?int { return $this->id; }
    public function getClient(): Client { return $this->client; }
    public function setClient(Client $c): self { $this->client = $c; return $this; }
    public function getVehicle(): Vehicle { return $this->vehicle; }
    public function setVehicle(Vehicle $v): self { $this->vehicle = $v; return $this; }
    public function getEmployee(): ?Employee { return $this->employee; }
    public function setEmployee(?Employee $e): self { $this->employee = $e; return $this; }
    public function getStartDatetime(): ?\DateTimeInterface { return $this->startDatetime; }
    public function setStartDatetime(?\DateTimeInterface $d): self { $this->startDatetime = $d; return $this; }
    public function getEndDatetime(): ?\DateTimeInterface { return $this->endDatetime; }
    public function setEndDatetime(?\DateTimeInterface $d): self { $this->endDatetime = $d; return $this; }
    public function getTotalAmount(): ?string { return $this->totalAmount; }
    public function setTotalAmount(?string $a): self { $this->totalAmount = $a; return $this; }
    public function getStatus(): string { return $this->status; }
    public function setStatus(string $s): self { $this->status = $s; return $this; }
}
