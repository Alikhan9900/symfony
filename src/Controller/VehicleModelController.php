<?php

namespace App\Controller;

use App\Entity\VehicleModel;
use App\Repository\VehicleModelRepository;
use App\Repository\ManufacturerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/vehicle-models', name: 'api_vehicle_models_')]
class VehicleModelController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private VehicleModelRepository $repo,
        private ManufacturerRepository $manufacturerRepo
    ) {
    }

    #[Route('', methods: ['GET'], name: 'list')]
    public function list(): JsonResponse
    {
        return $this->json($this->repo->findAll());
    }

    #[Route('', methods: ['POST'], name: 'create')]
    public function create(Request $request): JsonResponse
    {
        $d = json_decode($request->getContent(), true);

        if (empty($d['name']) || empty($d['manufacturer_id'])) {
            return $this->json(['error' => 'name and manufacturer_id are required'], 400);
        }

        $manufacturer = $this->manufacturerRepo->find($d['manufacturer_id']);
        if (!$manufacturer) {
            return $this->json(['error' => 'manufacturer not found'], 404);
        }

        $model = new VehicleModel();
        $model->setName($d['name']);
        $model->setManufacturer($manufacturer);
        $model->setSeats($d['seats'] ?? null);

        $this->em->persist($model);
        $this->em->flush();

        return $this->json(['id' => $model->getId()], 201);
    }

    #[Route('/{id}', methods: ['GET'], name: 'get')]
    public function getOne(VehicleModel $model): JsonResponse
    {
        return $this->json($model);
    }

    #[Route('/{id}', methods: ['PUT'], name: 'update')]
    public function update(Request $request, VehicleModel $model): JsonResponse
    {
        $d = json_decode($request->getContent(), true);

        if (isset($d['name'])) {
            $model->setName($d['name']);
        }
        if (isset($d['seats'])) {
            $model->setSeats((int) $d['seats']);
        }

        $this->em->flush();

        return $this->json(['message' => 'updated']);
    }

    #[Route('/{id}', methods: ['DELETE'], name: 'delete')]
    public function delete(VehicleModel $model): JsonResponse
    {
        $this->em->remove($model);
        $this->em->flush();

        return $this->json(['message' => 'deleted']);
    }
}
