<?php

namespace App\Controller;

use App\Entity\Rental;
use App\Repository\RentalRepository;
use App\Repository\ClientRepository;
use App\Repository\VehicleRepository;
use App\Service\RequestValidator;
use App\Service\RentalManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/rentals', name: 'api_rentals_')]
class RentalController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private RentalRepository $repo,
        private ClientRepository $clientRepo,
        private VehicleRepository $vehicleRepo,
        private RequestValidator $validator,
        private RentalManager $rentalManager
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

        $errors = $this->validator->validateRentalData($data);
        if ($errors) {
            return $this->json(['errors' => $errors], 400);
        }

        $client = $this->clientRepo->find($data['client_id']);
        $vehicle = $this->vehicleRepo->find($data['vehicle_id']);

        if (!$client || !$vehicle) {
            return $this->json(['error' => 'client or vehicle not found'], 404);
        }

        try {
            $rental = $this->rentalManager->createRental(
                $client,
                $vehicle,
                new \DateTime($data['start']),
                new \DateTime($data['end']),
                (float) $data['total']
            );
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }

        return $this->json(['id' => $rental->getId()], 201);
    }

    #[Route('/{id}', methods: ['GET'], name: 'get')]
    public function getOne(Rental $rental): JsonResponse
    {
        return $this->json($rental);
    }

    #[Route('/{id}', methods: ['PUT'], name: 'update')]
    public function update(Request $req, Rental $rental): JsonResponse
    {
        $d = json_decode($req->getContent(), true);

        if (isset($d['status'])) {
            $rental->setStatus($d['status']);
        }

        $this->em->flush();

        return $this->json(['message' => 'updated']);
    }

    #[Route('/{id}', methods: ['DELETE'], name: 'delete')]
    public function delete(Rental $rental): JsonResponse
    {
        $this->em->remove($rental);
        $this->em->flush();

        return $this->json(['message' => 'deleted']);
    }
}
