<?php

namespace App\Service;

use App\Entity\Payment;
use App\Entity\Rental;
use Doctrine\ORM\EntityManagerInterface;

class PaymentService
{
    public function __construct(
        private EntityManagerInterface $em,
        private RequestCheckerService $checker
    ) {}

    public function create(array $data, Rental $rental): Payment
    {
        $payment = new Payment();

        $payment->setRental($rental);
        $payment->setAmount($data['amount']);

        if (isset($data['paidAt'])) {
            $payment->setPaidAt(new \DateTime($data['paidAt']));
        }

        $payment->setMethod($data['method'] ?? null);
        $payment->setTransactionId($data['transactionId'] ?? null);

        $this->checker->validateRequestDataByConstraints($payment);
        $this->em->persist($payment);

        return $payment;
    }

    public function update(Payment $payment, array $data): Payment
    {
        if (isset($data['paidAt'])) {
            $payment->setPaidAt(new \DateTime($data['paidAt']));
        }

        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($payment, $method)) {
                $payment->$method($value);
            }
        }

        $this->checker->validateRequestDataByConstraints($payment);

        return $payment;
    }
}
