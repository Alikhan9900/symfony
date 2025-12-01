<?php

namespace App\Controller;

use App\Entity\Address;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/addresses', name: 'api_addresses_')]
class AddressController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private AddressRepository $repo
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

        $address = new Address();
        $address->setCountry($d['country'] ?? null);
        $address->setCity($d['city'] ?? null);
        $address->setStreet($d['street'] ?? null);
        $address->setZip($d['zip'] ?? null);

        $this->em->persist($address);
        $this->em->flush();

        return $this->json(['id' => $address->getId()], 201);
    }

    #[Route('/{id}', methods: ['GET'], name: 'get')]
    public function getOne(Address $address): JsonResponse
    {
        return $this->json($address);
    }

    #[Route('/{id}', methods: ['PUT'], name: 'update')]
    public function update(Request $request, Address $address): JsonResponse
    {
        $d = json_decode($request->getContent(), true);

        if (isset($d['country'])) $address->setCountry($d['country']);
        if (isset($d['city'])) $address->setCity($d['city']);
        if (isset($d['street'])) $address->setStreet($d['street']);
        if (isset($d['zip'])) $address->setZip($d['zip']);

        $this->em->flush();

        return $this->json(['message' => 'updated']);
    }

    #[Route('/{id}', methods: ['DELETE'], name: 'delete')]
    public function delete(Address $address): JsonResponse
    {
        $this->em->remove($address);
        $this->em->flush();

        return $this->json(['message' => 'deleted']);
    }
}
