<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Repository\BranchRepository;
use App\Repository\RentalRepository;
use App\Repository\EmployeeRepository;
use App\Service\EmployeeService;
use App\Service\DeleteProtectionService;
use App\Service\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/employees')]
class EmployeeController
{
    private const REQUIRED_FIELDS = ['firstName', 'lastName'];

    public function __construct(
        private EmployeeService $service,
        private BranchRepository $branchRepo,
        private RentalRepository $rentalRepo,
        private DeleteProtectionService $deleteProtector,
        private RequestCheckerService $checker,
        private EntityManagerInterface $em
    ) {}

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $this->checker->check($data, self::REQUIRED_FIELDS);

        $branch = isset($data['branchId'])
            ? $this->branchRepo->find($data['branchId'])
            : null;

        $employee = $this->service->create($data, $branch);
        $this->em->flush();

        return new JsonResponse($employee, 201);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(Request $request, Employee $employee): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $this->service->update($employee, $data);
        $this->em->flush();

        return new JsonResponse($employee);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(Employee $employee): JsonResponse
    {
        $this->deleteProtector->denyIfHasRelations($employee, [
            'rentals' => [$this->rentalRepo, 'employee']
        ]);

        $this->em->remove($employee);
        $this->em->flush();

        return new JsonResponse(['message' => 'Deleted']);
    }

    #[Route('', methods: ['GET'])]
    public function getCollection(Request $request, EmployeeRepository $repo): JsonResponse
    {
        $query = $request->query->all();

        $itemsPerPage = $query['itemsPerPage'] ?? 10;
        $page = $query['page'] ?? 1;

        $result = $repo->getAllByFilter($query, (int)$itemsPerPage, (int)$page);

        return new JsonResponse($result);
    }

}
