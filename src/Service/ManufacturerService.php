<?php

namespace App\Service;

use App\Entity\Manufacturer;
use Doctrine\ORM\EntityManagerInterface;

class ManufacturerService
{
    public function __construct(
        private EntityManagerInterface $em,
        private RequestCheckerService $checker
    ) {}

    public function create(array $data): Manufacturer
    {
        $manufacturer = new Manufacturer();
        $manufacturer->setName($data['name']);

        $this->checker->validateRequestDataByConstraints($manufacturer);
        $this->em->persist($manufacturer);

        return $manufacturer;
    }

    public function update(Manufacturer $manufacturer, array $data): Manufacturer
    {
        if (isset($data['name'])) {
            $manufacturer->setName($data['name']);
        }

        $this->checker->validateRequestDataByConstraints($manufacturer);
        return $manufacturer;
    }
}
