<?php

namespace App\Controller;

use App\Entity\Branch;
use App\Repository\BranchRepository;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/branches', name: 'api_branches_')]
class BranchController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private BranchRepository $repo,
        private AddressRepository $addressRepo
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

        if (empty($d['name'])) {
            return $this->json(['error' => 'name is required'], 400);
        }

        $branch = new Branch();
        $branch->setName($d['name']);

        if (!empty($d['address_id'])) {
            $address = $this->addressRepo->find($d['address_id']);
            if (!$address) {
                return $this->json(['error' => 'address not found'], 404);
            }
            $branch->setAddress($address);
        }

        $this->em->persist($branch);
        $this->em->flush();

        return $this->json(['id' => $branch->getId()], 201);
    }

    #[Route('/{id}', methods: ['GET'], name: 'get')]
    public function getOne(Branch $branch): JsonResponse
    {
        return $this->json($branch);
    }

    #[Route('/{id}', methods: ['PUT'], name: 'update')]
    public function update(Request $request, Branch $branch): JsonResponse
    {
        $d = json_decode($request->getContent(), true);

        if (isset($d['name'])) {
            $branch->setName($d['name']);
        }

        if (!empty($d['address_id'])) {
            $address = $this->addressRepo->find($d['address_id']);
            if (!$address) {
                return $this->json(['error' => 'address not found'], 404);
            }
            $branch->setAddress($address);
        }

        $this->em->flush();

        return $this->json(['message' => 'updated']);
    }

    #[Route('/{id}', methods: ['DELETE'], name: 'delete')]
    public function delete(Branch $branch): JsonResponse
    {
        $this->em->remove($branch);
        $this->em->flush();

        return $this->json(['message' => 'deleted']);
    }
}
