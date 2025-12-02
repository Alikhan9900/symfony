<?php

namespace App\Controller;

use App\Entity\Payment;
use App\Repository\RentalRepository;
use App\Service\PaymentService;
use App\Service\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/payments')]
class PaymentController
{
    private const REQUIRED_FIELDS = ['rentalId', 'amount'];

    public function __construct(
        private PaymentService $service,
        private RentalRepository $rentalRepo,
        private RequestCheckerService $checker,
        private EntityManagerInterface $em
    ) {}

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $this->checker->check($data, self::REQUIRED_FIELDS);

        $rental = $this->rentalRepo->find($data['rentalId']);

        $payment = $this->service->create($data, $rental);
        $this->em->flush();

        return new JsonResponse($payment, 201);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(Request $request, Payment $payment): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $this->service->update($payment, $data);
        $this->em->flush();

        return new JsonResponse($payment);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(Payment $payment): JsonResponse
    {
        $this->em->remove($payment);
        $this->em->flush();

        return new JsonResponse(['message' => 'Deleted']);
    }
}
