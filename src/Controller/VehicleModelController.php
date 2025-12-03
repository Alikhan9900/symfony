<?php

namespace App\Controller;

use App\Entity\VehicleModel;
use App\Repository\ManufacturerRepository;
use App\Repository\VehicleRepository;
use App\Service\VehicleModelService;
use App\Service\DeleteProtectionService;
use App\Service\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\VehicleModelRepository;

#[Route('/vehicle-models')]
class VehicleModelController
{
    private const REQUIRED_FIELDS = ['manufacturerId', 'name'];

    public function __construct(
        private VehicleModelService $service,
        private ManufacturerRepository $manufacturerRepo,
        private VehicleRepository $vehicleRepo,
        private DeleteProtectionService $deleteProtector,
        private RequestCheckerService $checker,
        private EntityManagerInterface $em
    ) {}

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $this->checker->check($data, self::REQUIRED_FIELDS);

        $manufacturer = $this->manufacturerRepo->find($data['manufacturerId']);

        $model = $this->service->create($data, $manufacturer);
        $this->em->flush();

        return new JsonResponse($model, 201);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(Request $request, VehicleModel $model): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $this->service->update($model, $data);
        $this->em->flush();

        return new JsonResponse($model);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(VehicleModel $model): JsonResponse
    {
        $this->deleteProtector->denyIfHasRelations($model, [
            'vehicles' => [$this->vehicleRepo, 'model']
        ]);

        $this->em->remove($model);
        $this->em->flush();

        return new JsonResponse(['message' => 'Deleted']);
    }



    #[Route('', methods: ['GET'])]
    public function getCollection(Request $request, VehicleModelRepository $repo): JsonResponse
    {
        $query = $request->query->all();

        $itemsPerPage = isset($query['itemsPerPage']) ? (int)$query['itemsPerPage'] : 10;
        $page         = isset($query['page']) ? (int)$query['page'] : 1;

        $result = $repo->getAllByFilter($query, $itemsPerPage, $page);

        return new JsonResponse($result);
    }


}
