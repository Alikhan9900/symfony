<?php

namespace App\Service;

use App\Entity\Client;
use App\Entity\Address;
use Doctrine\ORM\EntityManagerInterface;

class ClientService
{
    public function __construct(
        private EntityManagerInterface $em,
        private RequestCheckerService $checker,
        private AddressService $addressService
    ) {}

    public function create(array $data): Client
    {
        $client = new Client();

        $client->setFirstName($data['firstName']);
        $client->setLastName($data['lastName']);
        $client->setEmail($data['email'] ?? null);
        $client->setPhone($data['phone'] ?? null);
        $client->setDriverLicense($data['driverLicense'] ?? null);

        if (isset($data['address'])) {
            $client->setAddress($this->addressService->create($data['address']));
        }

        $this->checker->validateRequestDataByConstraints($client);
        $this->em->persist($client);

        return $client;
    }

    public function update(Client $client, array $data): Client
    {
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($client, $method)) {
                $client->$method($value);
            }
        }

        if (isset($data['address'])) {
            if ($client->getAddress()) {
                $this->addressService->update($client->getAddress(), $data['address']);
            } else {
                $client->setAddress($this->addressService->create($data['address']));
            }
        }

        $this->checker->validateRequestDataByConstraints($client);
        return $client;
    }
}
