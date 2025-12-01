<?php

namespace App\Controller;

use App\Entity\Manufacturer;
use App\Repository\ManufacturerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/manufacturers', name: 'api_manufacturers_')]
class ManufacturerController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private ManufacturerRepository $repo
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
        $data = json_decode($request->getContent(), true);

        if (empty($data['name'])) {
            return $this->json(['error' => 'name is required'], 400);
        }

        $manufacturer = new Manufacturer();
        $manufacturer->setName($data['name']);

        $this->em->persist($manufacturer);
        $this->em->flush();

        return $this->json(['id' => $manufacturer->getId()], 201);
    }

    #[Route('/{id}', methods: ['GET'], name: 'get')]
    public function getOne(Manufacturer $manufacturer): JsonResponse
    {
        return $this->json($manufacturer);
    }

    #[Route('/{id}', methods: ['PUT'], name: 'update')]
    public function update(Request $request, Manufacturer $manufacturer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['name'])) {
            $manufacturer->setName($data['name']);
        }

        $this->em->flush();

        return $this->json(['message' => 'updated']);
    }

    #[Route('/{id}', methods: ['DELETE'], name: 'delete')]
    public function delete(Manufacturer $manufacturer): JsonResponse
    {
        $this->em->remove($manufacturer);
        $this->em->flush();

        return $this->json(['message' => 'deleted']);
    }
}
