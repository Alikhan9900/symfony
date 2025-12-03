<?php

namespace App\Controller;

use App\Entity\MaintenanceRecord;
use App\Repository\VehicleRepository;
use App\Repository\MaintenanceRecordRepository;
use App\Service\MaintenanceRecordService;
use App\Service\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/maintenance-records')]
class MaintenanceRecordController
{
    private const REQUIRED_FIELDS = ['vehicleId', 'description', 'cost'];

    public function __construct(
        private MaintenanceRecordService $service,
        private VehicleRepository $vehicleRepo,
        private RequestCheckerService $checker,
        private EntityManagerInterface $em
    ) {}

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $this->checker->check($data, self::REQUIRED_FIELDS);

        $vehicle = $this->vehicleRepo->find($data['vehicleId']);

        $record = $this->service->create($data, $vehicle);
        $this->em->flush();

        return new JsonResponse($record, 201);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(Request $request, MaintenanceRecord $record): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $this->service->update($record, $data);
        $this->em->flush();

        return new JsonResponse($record);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(MaintenanceRecord $record): JsonResponse
    {
        $this->em->remove($record);
        $this->em->flush();

        return new JsonResponse(['message' => 'Deleted']);
    }

    #[Route('', methods: ['GET'])]
    public function getCollection(Request $request, MaintenanceRecordRepository $repo): JsonResponse
    {
        $query = $request->query->all();

        $itemsPerPage = $query['itemsPerPage'] ?? 10;
        $page = $query['page'] ?? 1;

        $result = $repo->getAllByFilter($query, (int)$itemsPerPage, (int)$page);

        return new JsonResponse($result);
    }

}
