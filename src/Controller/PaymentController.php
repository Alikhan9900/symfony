<?php

namespace App\Controller;

use App\Entity\Payment;
use App\Repository\PaymentRepository;
use App\Repository\RentalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/payments', name: 'api_payments_')]
class PaymentController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private PaymentRepository $repo,
        private RentalRepository $rentalRepo
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

        if (empty($d['rental_id']) || empty($d['amount'])) {
            return $this->json(['error' => 'rental_id and amount required'], 400);
        }

        $rental = $this->rentalRepo->find($d['rental_id']);
        if (!$rental) {
            return $this->json(['error' => 'rental not found'], 404);
        }

        $p = new Payment();
        $p->setRental($rental);
        $p->setAmount((string) $d['amount']);
        $p->setMethod($d['method'] ?? null);
        $p->setTransactionId($d['transaction_id'] ?? null);
        $p->setPaidAt(new \DateTime());

        $this->em->persist($p);
        $this->em->flush();

        return $this->json(['id' => $p->getId()], 201);
    }

    #[Route('/{id}', methods: ['GET'], name: 'get')]
    public function getOne(Payment $payment): JsonResponse
    {
        return $this->json($payment);
    }

    #[Route('/{id}', methods: ['DELETE'], name: 'delete')]
    public function delete(Payment $payment): JsonResponse
    {
        $this->em->remove($payment);
        $this->em->flush();

        return $this->json(['message' => 'deleted']);
    }
}
