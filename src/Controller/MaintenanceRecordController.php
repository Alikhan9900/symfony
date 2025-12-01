<?php

namespace App\Controller;

use App\Entity\MaintenanceRecord;
use App\Repository\MaintenanceRecordRepository;
use App\Repository\VehicleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/maintenance-records', name: 'api_maintenance_')]
class MaintenanceRecordController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private MaintenanceRecordRepository $repo,
        private VehicleRepository $vehicleRepo
    ) {
    }

    #[Route('', methods: ['GET'], name: 'list')]
    public function list(): JsonResponse
    {
        return $this->json($this->repo->findAll());
    }

    #[Route('', methods: ['POST'], name: 'create')]
    public function create(Request $req): JsonResponse
    {
        $d = json_decode($req->getContent(), true);

        if (empty($d['vehicle_id'])) {
            return $this->json(['error' => 'vehicle_id is required'], 400);
        }

        $vehicle = $this->vehicleRepo->find($d['vehicle_id']);
        if (!$vehicle) {
            return $this->json(['error' => 'vehicle not found'], 404);
        }

        $m = new MaintenanceRecord();
        $m->setVehicle($vehicle);
        $m->setDescription($d['description'] ?? null);
        $m->setCost(isset($d['cost']) ? (string)$d['cost'] : null);
        $m->setPerformedAt(new \DateTime($d['performed_at'] ?? 'now'));

        $this->em->persist($m);
        $this->em->flush();

        return $this->json(['id' => $m->getId()], 201);
    }

    #[Route('/{id}', methods: ['GET'], name: 'get')]
    public function getOne(MaintenanceRecord $record): JsonResponse
    {
        return $this->json($record);
    }

    #[Route('/{id}', methods: ['PUT'], name: 'update')]
    public function update(Request $req, MaintenanceRecord $record): JsonResponse
    {
        $d = json_decode($req->getContent(), true);

        if (isset($d['description'])) $record->setDescription($d['description']);
        if (isset($d['cost'])) $record->setCost((string)$d['cost']);

        $this->em->flush();

        return $this->json(['message' => 'updated']);
    }

    #[Route('/{id}', methods: ['DELETE'], name: 'delete')]
    public function delete(MaintenanceRecord $record): JsonResponse
    {
        $this->em->remove($record);
        $this->em->flush();

        return $this->json(['message' => 'deleted']);
    }
}
