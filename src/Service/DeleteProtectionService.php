<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class DeleteProtectionService
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    /**
     * Перевіряє, чи існують записи, які залежні від сутності.
     */
    public function denyIfHasRelations(object $entity, array $relationChecks): void
    {
        foreach ($relationChecks as $relationName => $repoAndField) {

            [$repository, $field] = $repoAndField;

            $count = $repository->count([$field => $entity]);

            if ($count > 0) {
                throw new \RuntimeException(
                    sprintf(
                        "Cannot delete %s because related %s exist.",
                        get_class($entity),
                        $relationName
                    )
                );
            }
        }
    }
}
