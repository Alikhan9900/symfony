<?php

namespace App\Controller;

use App\Entity\Branch;
use App\Repository\AddressRepository;
use App\Repository\VehicleRepository;
use App\Repository\EmployeeRepository;
use App\Service\BranchService;
use App\Service\DeleteProtectionService;
use App\Service\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\BranchRepository;

#[Route('/branches')]
class BranchController
{
    private const REQUIRED_FIELDS = ['name', 'addressId'];

    public function __construct(
        private BranchService $service,
        private AddressRepository $addressRepo,
        private VehicleRepository $vehicleRepo,
        private EmployeeRepository $employeeRepo,
        private DeleteProtectionService $deleteProtector,
        private RequestCheckerService $checker,
        private EntityManagerInterface $em
    ) {}

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $this->checker->check($data, self::REQUIRED_FIELDS);

        $address = $this->addressRepo->find($data['addressId']);

        $branch = $this->service->create($data, $address);
        $this->em->flush();

        return new JsonResponse($branch, 201);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(Request $request, Branch $branch): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $this->service->update($branch, $data);
        $this->em->flush();

        return new JsonResponse($branch);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(Branch $branch): JsonResponse
    {
        $this->deleteProtector->denyIfHasRelations($branch, [
            'vehicles' => [$this->vehicleRepo, 'branch'],
            'employees' => [$this->employeeRepo, 'branch']
        ]);

        $this->em->remove($branch);
        $this->em->flush();

        return new JsonResponse(['message' => 'Deleted']);
    }

    #[Route('', methods: ['GET'])]
    public function getCollection(Request $request, BranchRepository $repo): JsonResponse
    {
        $query = $request->query->all();

        $itemsPerPage = isset($query['itemsPerPage']) ? (int)$query['itemsPerPage'] : 10;
        $page         = isset($query['page']) ? (int)$query['page'] : 1;

        $result = $repo->getAllByFilter($query, $itemsPerPage, $page);

        return new JsonResponse($result);
    }

}
