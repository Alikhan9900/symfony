<?php

namespace App\Service;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class RequestValidator
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    public function validateRentalData(array $data): array
    {
        $constraints = new Assert\Collection([
            'client_id' => new Assert\Required([
                new Assert\NotBlank(),
                new Assert\Regex('/^\d+$/'),
            ]),
            'vehicle_id' => new Assert\Required([
                new Assert\NotBlank(),
                new Assert\Regex('/^\d+$/'),
            ]),
            'start' => new Assert\Required([
                new Assert\NotBlank(),
                new Assert\DateTime(),
            ]),
            'end' => new Assert\Required([
                new Assert\NotBlank(),
                new Assert\DateTime(),
            ]),
            'total' => new Assert\Required([
                new Assert\NotBlank(),
                new Assert\Type('numeric'),
            ]),
        ]);

        $violations = $this->validator->validate($data, $constraints);

        $errors = [];
        foreach ($violations as $v) {
            $errors[] = "[" . $v->getPropertyPath() . "]: " . $v->getMessage();
        }

        return $errors;
    }
}
