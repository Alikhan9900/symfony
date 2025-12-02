<?php

namespace App\Service;

use App\Entity\MaintenanceRecord;
use App\Entity\Vehicle;
use Doctrine\ORM\EntityManagerInterface;

class MaintenanceRecordService
{
    public function __construct(
        private EntityManagerInterface $em,
        private RequestCheckerService $checker
    ) {}

    public function create(array $data, Vehicle $vehicle): MaintenanceRecord
    {
        $record = new MaintenanceRecord();

        $record->setVehicle($vehicle);

        if (isset($data['performedAt'])) {
            $record->setPerformedAt(new \DateTime($data['performedAt']));
        }

        $record->setDescription($data['description'] ?? null);
        $record->setCost($data['cost'] ?? null);

        $this->checker->validateRequestDataByConstraints($record);
        $this->em->persist($record);

        return $record;
    }

    public function update(MaintenanceRecord $record, array $data): MaintenanceRecord
    {
        if (isset($data['performedAt'])) {
            $record->setPerformedAt(new \DateTime($data['performedAt']));
        }

        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($record, $method)) {
                $record->$method($value);
            }
        }

        $this->checker->validateRequestDataByConstraints($record);

        return $record;
    }
}
