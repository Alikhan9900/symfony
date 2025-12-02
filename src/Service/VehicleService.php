<?php

namespace App\Service;

use App\Entity\Vehicle;
use App\Entity\VehicleModel;
use App\Entity\Branch;
use Doctrine\ORM\EntityManagerInterface;

class VehicleService
{
    public function __construct(
        private EntityManagerInterface $em,
        private RequestCheckerService $checker
    ) {}

    public function create(array $data, VehicleModel $model, ?Branch $branch): Vehicle
    {
        $vehicle = new Vehicle();

        $vehicle->setModel($model);
        $vehicle->setVin($data['vin']);
        $vehicle->setStatus($data['status'] ?? 'available');
        $vehicle->setYear($data['year'] ?? null);
        $vehicle->setMileage($data['mileage'] ?? 0);
        $vehicle->setBranch($branch);

        $this->checker->validateRequestDataByConstraints($vehicle);
        $this->em->persist($vehicle);

        return $vehicle;
    }

    public function update(Vehicle $vehicle, array $data): Vehicle
    {
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($vehicle, $method)) {
                $vehicle->$method($value);
            }
        }

        $this->checker->validateRequestDataByConstraints($vehicle);
        return $vehicle;
    }
}
