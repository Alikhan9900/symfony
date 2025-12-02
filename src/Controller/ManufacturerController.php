<?php

namespace App\Controller;

use App\Entity\Manufacturer;
use App\Service\ManufacturerService;
use App\Repository\VehicleModelRepository;
use App\Service\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\DeleteProtectionService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/manufacturers')]
class ManufacturerController
{
    private const REQUIRED_FIELDS = ['name'];

    public function __construct(
        private ManufacturerService $service,
        private RequestCheckerService $checker,
        private DeleteProtectionService $deleteProtector,
        private VehicleModelRepository $vehicleModelRepo,
        private EntityManagerInterface $em
    ) {}

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $this->checker->check($data, self::REQUIRED_FIELDS);

        $manufacturer = $this->service->create($data);
        $this->em->flush();

        return new JsonResponse($manufacturer, 201);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(Request $request, Manufacturer $manufacturer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $this->service->update($manufacturer, $data);
        $this->em->flush();

        return new JsonResponse($manufacturer);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(Manufacturer $manufacturer): JsonResponse
    {
        $this->deleteProtector->denyIfHasRelations($manufacturer, [
            'vehicle models' => [$this->vehicleModelRepo, 'manufacturer']
        ]);

        $this->em->remove($manufacturer);
        $this->em->flush();

        return new JsonResponse(['message' => 'Deleted']);
    }


}
