<?php

namespace App\Service;

use App\Entity\Branch;
use App\Entity\Address;
use Doctrine\ORM\EntityManagerInterface;

class BranchService
{
    public function __construct(
        private EntityManagerInterface $em,
        private RequestCheckerService $checker,
        private AddressService $addressService
    ) {}

    public function create(array $data): Branch
    {
        $branch = new Branch();
        $branch->setName($data['name']);

        if (isset($data['address'])) {
            $address = $this->addressService->create($data['address']);
            $branch->setAddress($address);
        }

        $this->checker->validateRequestDataByConstraints($branch);
        $this->em->persist($branch);

        return $branch;
    }

    public function update(Branch $branch, array $data): Branch
    {
        if (isset($data['name'])) {
            $branch->setName($data['name']);
        }

        if (isset($data['address'])) {
            if ($branch->getAddress()) {
                $this->addressService->update($branch->getAddress(), $data['address']);
            } else {
                $branch->setAddress($this->addressService->create($data['address']));
            }
        }

        $this->checker->validateRequestDataByConstraints($branch);
        return $branch;
    }
}
