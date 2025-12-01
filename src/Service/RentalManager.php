<?php

namespace App\Service;

use App\Entity\Client;
use App\Entity\Vehicle;
use App\Entity\Rental;
use Doctrine\ORM\EntityManagerInterface;

class RentalManager
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function createRental(
        Client $client,
        Vehicle $vehicle,
        \DateTimeInterface $start,
        \DateTimeInterface $end,
        float $total
    ): Rental {
        if ($vehicle->getStatus() !== 'available') {
            throw new \Exception('Vehicle not available');
        }

        $rental = new Rental();
        $rental->setClient($client);
        $rental->setVehicle($vehicle);
        $rental->setStartDatetime($start);
        $rental->setEndDatetime($end);
        $rental->setTotalAmount((string) $total);
        $rental->setStatus('active');

        $vehicle->setStatus('rented');

        $this->em->persist($rental);
        $this->em->flush();

        return $rental;
    }

    public function closeRental(Rental $rental): void
    {
        $rental->setStatus('closed');
        $rental->getVehicle()->setStatus('available');

        $this->em->flush();
    }
}
