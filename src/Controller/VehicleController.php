<?php

namespace App\Controller;

use App\Entity\Vehicle;
use App\Repository\VehicleModelRepository;
use App\Repository\BranchRepository;
use App\Repository\RentalRepository;
use App\Repository\MaintenanceRecordRepository;
use App\Service\VehicleService;
use App\Service\DeleteProtectionService;
use App\Service\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\VehicleRepository;

#[Route('/vehicles')]
class VehicleController
{
    private const REQUIRED_FIELDS = ['modelId', 'vin', 'mileage', 'status'];

    public function __construct(
        private VehicleService $service,
        private VehicleModelRepository $modelRepo,
        private BranchRepository $branchRepo,
        private RentalRepository $rentalRepo,
        private MaintenanceRecordRepository $maintenanceRepo,
        private DeleteProtectionService $deleteProtector,
        private RequestCheckerService $checker,
        private EntityManagerInterface $em
    ) {}

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $this->checker->check($data, self::REQUIRED_FIELDS);

        $model = $this->modelRepo->find($data['modelId']);
        $branch = isset($data['branchId']) ? $this->branchRepo->find($data['branchId']) : null;

        $vehicle = $this->service->create($data, $model, $branch);
        $this->em->flush();

        return new JsonResponse($vehicle, 201);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(Request $request, Vehicle $vehicle): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $this->service->update($vehicle, $data);
        $this->em->flush();

        return new JsonResponse($vehicle);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(Vehicle $vehicle): JsonResponse
    {
        $this->deleteProtector->denyIfHasRelations($vehicle, [
            'rentals' => [$this->rentalRepo, 'vehicle'],
            'maintenance records' => [$this->maintenanceRepo, 'vehicle'],
        ]);

        $this->em->remove($vehicle);
        $this->em->flush();

        return new JsonResponse(['message' => 'Deleted']);
    }

    #[Route('', methods: ['GET'])]
    public function getCollection(Request $request, VehicleRepository $repo): JsonResponse
    {
        $query = $request->query->all();

        $itemsPerPage = isset($query['itemsPerPage']) ? (int)$query['itemsPerPage'] : 10;
        $page         = isset($query['page']) ? (int)$query['page'] : 1;

        $result = $repo->getAllByFilter($query, $itemsPerPage, $page);

        return new JsonResponse($result);
    }

}
