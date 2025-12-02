<?php

namespace App\Service;

use App\Entity\Address;
use Doctrine\ORM\EntityManagerInterface;

class AddressService
{
    public function __construct(
        private EntityManagerInterface $em,
        private RequestCheckerService $checker
    ) {}

    public function create(array $data): Address
    {
        $address = new Address();

        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($address, $method)) {
                $address->$method($value);
            }
        }

        $this->checker->validateRequestDataByConstraints($address);

        $this->em->persist($address);
        return $address;
    }

    public function update(Address $address, array $data): Address
    {
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($address, $method)) {
                $address->$method($value);
            }
        }

        $this->checker->validateRequestDataByConstraints($address);

        return $address;
    }
}
