<?php

namespace App\Controller;

use App\Entity\Rental;
use App\Repository\ClientRepository;
use App\Repository\VehicleRepository;
use App\Repository\EmployeeRepository;
use App\Repository\PaymentRepository;
use App\Repository\RentalRepository;
use App\Service\RentalService;
use App\Service\DeleteProtectionService;
use App\Service\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/rentals')]
class RentalController
{
    private const REQUIRED_FIELDS = [
        'clientId',
        'vehicleId',
        'startDatetime',
        'endDatetime',
        'status'
    ];

    public function __construct(
        private RentalService $service,
        private ClientRepository $clientRepo,
        private VehicleRepository $vehicleRepo,
        private EmployeeRepository $employeeRepo,
        private PaymentRepository $paymentRepo,
        private DeleteProtectionService $deleteProtector,
        private RequestCheckerService $checker,
        private EntityManagerInterface $em
    ) {}

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $this->checker->check($data, self::REQUIRED_FIELDS);

        $client = $this->clientRepo->find($data['clientId']);
        $vehicle = $this->vehicleRepo->find($data['vehicleId']);
        $employee = isset($data['employeeId'])
            ? $this->employeeRepo->find($data['employeeId'])
            : null;

        $rental = $this->service->create($data, $client, $vehicle, $employee);
        $this->em->flush();

        return new JsonResponse($rental, 201);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(Request $request, Rental $rental): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $this->service->update($rental, $data);
        $this->em->flush();

        return new JsonResponse($rental);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(Rental $rental): JsonResponse
    {
        $this->deleteProtector->denyIfHasRelations($rental, [
            'payments' => [$this->paymentRepo, 'rental']
        ]);

        $this->em->remove($rental);
        $this->em->flush();

        return new JsonResponse(['message' => 'Deleted']);
    }

    #[Route('', methods: ['GET'])]
    public function getCollection(Request $request, RentalRepository $repo): JsonResponse
    {
        $query = $request->query->all();

        $itemsPerPage = $query['itemsPerPage'] ?? 10;
        $page = $query['page'] ?? 1;

        $result = $repo->getAllByFilter($query, (int)$itemsPerPage, (int)$page);

        return new JsonResponse($result);
    }

}
