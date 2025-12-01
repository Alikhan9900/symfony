<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Rental
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Client::class)]
    #[ORM\JoinColumn(nullable:false)]
    private Client $client;

    #[ORM\ManyToOne(targetEntity: Vehicle::class)]
    #[ORM\JoinColumn(nullable:false)]
    private Vehicle $vehicle;

    #[ORM\ManyToOne(targetEntity: Employee::class)]
    private ?Employee $employee = null;

    #[ORM\Column(type:"datetime", nullable:true)]
    private ?\DateTimeInterface $startDatetime = null;

    #[ORM\Column(type:"datetime", nullable:true)]
    private ?\DateTimeInterface $endDatetime = null;

    #[ORM\Column(type:"decimal", precision:10, scale:2, nullable:true)]
    private ?string $totalAmount = null;

    #[ORM\Column(type:"string", length:50)]
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
