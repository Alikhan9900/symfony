<?php

namespace App\Controller;

use App\Entity\Client;
use App\Repository\ClientRepository;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/clients', name: 'api_clients_')]
class ClientController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private ClientRepository $repo,
        private AddressRepository $addressRepo
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

        $client = new Client();
        $client->setFirstName($d['firstName']);
        $client->setLastName($d['lastName']);
        $client->setEmail($d['email'] ?? null);
        $client->setPhone($d['phone'] ?? null);
        $client->setDriverLicense($d['driverLicense'] ?? null);

        if (!empty($d['address_id'])) {
            $address = $this->addressRepo->find($d['address_id']);
            if (!$address) {
                return $this->json(['error' => 'address not found'], 404);
            }
            $client->setAddress($address);
        }

        $this->em->persist($client);
        $this->em->flush();

        return $this->json(['id' => $client->getId()], 201);
    }

    #[Route('/{id}', methods: ['GET'], name: 'get')]
    public function getOne(Client $client): JsonResponse
    {
        return $this->json($client);
    }

    #[Route('/{id}', methods: ['PUT'], name: 'update')]
    public function update(Request $req, Client $client): JsonResponse
    {
        $d = json_decode($req->getContent(), true);

        if (isset($d['firstName'])) $client->setFirstName($d['firstName']);
        if (isset($d['lastName'])) $client->setLastName($d['lastName']);
        if (isset($d['email'])) $client->setEmail($d['email']);
        if (isset($d['phone'])) $client->setPhone($d['phone']);
        if (isset($d['driverLicense'])) $client->setDriverLicense($d['driverLicense']);

        $this->em->flush();

        return $this->json(['message' => 'updated']);
    }

    #[Route('/{id}', methods: ['DELETE'], name: 'delete')]
    public function delete(Client $client): JsonResponse
    {
        $this->em->remove($client);
        $this->em->flush();

        return $this->json(['message' => 'deleted']);
    }
}
