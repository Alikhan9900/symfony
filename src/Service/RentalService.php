<?php

namespace App\Service;

use App\Entity\Rental;
use App\Entity\Client;
use App\Entity\Vehicle;
use App\Entity\Employee;
use Doctrine\ORM\EntityManagerInterface;

class RentalService
{
    public function __construct(
        private EntityManagerInterface $em,
        private RequestCheckerService $checker
    ) {}

    public function create(array $data, Client $client, Vehicle $vehicle, ?Employee $employee): Rental
    {
        $rental = new Rental();

        $rental->setClient($client);
        $rental->setVehicle($vehicle);
        $rental->setEmployee($employee);

        $rental->setStartDatetime(
            isset($data['startDatetime']) ? new \DateTime($data['startDatetime']) : null
        );

        $rental->setEndDatetime(
            isset($data['endDatetime']) ? new \DateTime($data['endDatetime']) : null
        );

        $rental->setTotalAmount($data['totalAmount'] ?? null);
        $rental->setStatus($data['status'] ?? 'new');

        $this->checker->validateRequestDataByConstraints($rental);
        $this->em->persist($rental);

        return $rental;
    }

    public function update(Rental $rental, array $data): Rental
    {
        if (isset($data['startDatetime'])) {
            $rental->setStartDatetime(new \DateTime($data['startDatetime']));
        }

        if (isset($data['endDatetime'])) {
            $rental->setEndDatetime(new \DateTime($data['endDatetime']));
        }

        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($rental, $method)) {
                $rental->$method($value);
            }
        }

        $this->checker->validateRequestDataByConstraints($rental);
        return $rental;
    }
}
