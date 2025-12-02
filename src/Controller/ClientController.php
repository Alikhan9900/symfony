<?php

namespace App\Controller;

use App\Entity\Client;
use App\Repository\RentalRepository;
use App\Service\ClientService;
use App\Service\DeleteProtectionService;
use App\Service\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/clients')]
class ClientController
{
    private const REQUIRED_FIELDS = ['firstName', 'lastName'];

    public function __construct(
        private ClientService $service,
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

        $client = $this->service->create($data);
        $this->em->flush();

        return new JsonResponse($client, 201);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(Request $request, Client $client): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $this->service->update($client, $data);
        $this->em->flush();

        return new JsonResponse($client);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(Client $client): JsonResponse
    {
        // Забороняємо видалення, якщо є оренди цього клієнта
        $this->deleteProtector->denyIfHasRelations($client, [
            'rentals' => [$this->rentalRepo, 'client']
        ]);

        $this->em->remove($client);
        $this->em->flush();

        return new JsonResponse(['message' => 'Deleted']);
    }
}
