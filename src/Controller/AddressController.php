<?php

namespace App\Controller;

use App\Entity\Address;
use App\Repository\ClientRepository;
use App\Repository\BranchRepository;
use App\Service\AddressService;
use App\Service\DeleteProtectionService;
use App\Service\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/addresses')]
class AddressController
{
    private const REQUIRED_FIELDS = ['country', 'city', 'street'];

    public function __construct(
        private AddressService $service,
        private ClientRepository $clientRepo,
        private BranchRepository $branchRepo,
        private DeleteProtectionService $deleteProtector,
        private RequestCheckerService $checker,
        private EntityManagerInterface $em
    ) {}

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $this->checker->check($data, self::REQUIRED_FIELDS);

        $address = $this->service->create($data);
        $this->em->flush();

        return new JsonResponse($address, 201);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(Request $request, Address $address): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $this->service->update($address, $data);
        $this->em->flush();

        return new JsonResponse($address);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(Address $address): JsonResponse
    {
        $this->deleteProtector->denyIfHasRelations($address, [
            'clients' => [$this->clientRepo, 'address'],
            'branches' => [$this->branchRepo, 'address']
        ]);

        $this->em->remove($address);
        $this->em->flush();

        return new JsonResponse(['message' => 'Deleted']);
    }
}
