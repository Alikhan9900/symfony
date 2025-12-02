<?php

namespace App\Service;

use App\Entity\VehicleModel;
use App\Entity\Manufacturer;
use Doctrine\ORM\EntityManagerInterface;

class VehicleModelService
{
    public function __construct(
        private EntityManagerInterface $em,
        private RequestCheckerService $checker
    ) {}

    public function create(array $data, Manufacturer $manufacturer): VehicleModel
    {
        $model = new VehicleModel();
        $model->setManufacturer($manufacturer);
        $model->setName($data['name']);

        if (isset($data['seats'])) {
            $model->setSeats($data['seats']);
        }

        $this->checker->validateRequestDataByConstraints($model);
        $this->em->persist($model);

        return $model;
    }

    public function update(VehicleModel $model, array $data): VehicleModel
    {
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($model, $method)) {
                $model->$method($value);
            }
        }

        $this->checker->validateRequestDataByConstraints($model);

        return $model;
    }
}
