<?php

namespace App\Controller;

use App\Entity\Vehicle;
use App\Repository\VehicleRepository;
use App\Repository\VehicleModelRepository;
use App\Repository\BranchRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/vehicles', name: 'api_vehicles_')]
class VehicleController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private VehicleRepository $repo,
        private VehicleModelRepository $modelRepo,
        private BranchRepository $branchRepo
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
        $data = json_decode($req->getContent(), true);

        if (empty($data['vin']) || empty($data['model_id'])) {
            return $this->json(['error' => 'vin and model_id required'], 400);
        }

        $model = $this->modelRepo->find($data['model_id']);
        if (!$model) {
            return $this->json(['error' => 'model not found'], 404);
        }

        $vehicle = new Vehicle();
        $vehicle->setVin($data['vin']);
        $vehicle->setModel($model);
        $vehicle->setYear($data['year'] ?? null);
        $vehicle->setMileage((int)($data['mileage'] ?? 0));
        $vehicle->setStatus($data['status'] ?? 'available');

        if (!empty($data['branch_id'])) {
            $branch = $this->branchRepo->find($data['branch_id']);
            if (!$branch) {
                return $this->json(['error' => 'branch not found'], 404);
            }
            $vehicle->setBranch($branch);
        }

        $this->em->persist($vehicle);
        $this->em->flush();

        return $this->json(['id' => $vehicle->getId()], 201);
    }

    #[Route('/{id}', methods: ['GET'], name: 'get')]
    public function getOne(Vehicle $vehicle): JsonResponse
    {
        return $this->json($vehicle);
    }

    #[Route('/{id}', methods: ['PUT'], name: 'update')]
    public function update(Request $req, Vehicle $vehicle): JsonResponse
    {
        $data = json_decode($req->getContent(), true);

        if (isset($data['mileage'])) $vehicle->setMileage((int)$data['mileage']);
        if (isset($data['status'])) $vehicle->setStatus($data['status']);
        if (isset($data['year'])) $vehicle->setYear((int)$data['year']);

        $this->em->flush();

        return $this->json(['message' => 'updated']);
    }

    #[Route('/{id}', methods: ['DELETE'], name: 'delete')]
    public function delete(Vehicle $vehicle): JsonResponse
    {
        $this->em->remove($vehicle);
        $this->em->flush();

        return $this->json(['message' => 'deleted']);
    }
}
