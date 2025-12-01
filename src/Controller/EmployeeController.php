<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Repository\EmployeeRepository;
use App\Repository\BranchRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/employees', name: 'api_employees_')]
class EmployeeController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private EmployeeRepository $repo,
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
        $d = json_decode($req->getContent(), true);

        if (empty($d['firstName']) || empty($d['lastName'])) {
            return $this->json(['error' => 'firstName & lastName required'], 400);
        }

        $e = new Employee();
        $e->setFirstName($d['firstName']);
        $e->setLastName($d['lastName']);
        $e->setEmail($d['email'] ?? null);

        if (!empty($d['branch_id'])) {
            $branch = $this->branchRepo->find($d['branch_id']);
            if (!$branch) {
                return $this->json(['error' => 'branch not found'], 404);
            }
            $e->setBranch($branch);
        }

        $this->em->persist($e);
        $this->em->flush();

        return $this->json(['id' => $e->getId()], 201);
    }

    #[Route('/{id}', methods: ['GET'], name: 'get')]
    public function getOne(Employee $employee): JsonResponse
    {
        return $this->json($employee);
    }

    #[Route('/{id}', methods: ['PUT'], name: 'update')]
    public function update(Request $req, Employee $employee): JsonResponse
    {
        $d = json_decode($req->getContent(), true);

        if (isset($d['firstName'])) $employee->setFirstName($d['firstName']);
        if (isset($d['lastName'])) $employee->setLastName($d['lastName']);
        if (isset($d['email'])) $employee->setEmail($d['email']);

        $this->em->flush();

        return $this->json(['message' => 'updated']);
    }

    #[Route('/{id}', methods: ['DELETE'], name: 'delete')]
    public function delete(Employee $employee): JsonResponse
    {
        $this->em->remove($employee);
        $this->em->flush();

        return $this->json(['message' => 'deleted']);
    }
}
