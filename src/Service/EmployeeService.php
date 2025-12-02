<?php

namespace App\Service;

use App\Entity\Employee;
use App\Entity\Branch;
use Doctrine\ORM\EntityManagerInterface;

class EmployeeService
{
    public function __construct(
        private EntityManagerInterface $em,
        private RequestCheckerService $checker
    ) {}

    public function create(array $data, ?Branch $branch): Employee
    {
        $employee = new Employee();

        $employee->setFirstName($data['firstName']);
        $employee->setLastName($data['lastName']);
        $employee->setEmail($data['email'] ?? null);
        $employee->setBranch($branch);

        $this->checker->validateRequestDataByConstraints($employee);
        $this->em->persist($employee);

        return $employee;
    }

    public function update(Employee $employee, array $data): Employee
    {
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($employee, $method)) {
                $employee->$method($value);
            }
        }

        $this->checker->validateRequestDataByConstraints($employee);
        return $employee;
    }
}
