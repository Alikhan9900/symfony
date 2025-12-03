<?php

namespace App\Repository;

use App\Entity\Employee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Employee>
 */
class EmployeeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Employee::class);
    }

    public function getAllByFilter(array $data, int $itemsPerPage, int $page): array
    {
        $qb = $this->createQueryBuilder('e');

        if (!empty($data['firstName'])) {
            $qb->andWhere('LOWER(e.firstName) LIKE :fn')
                ->setParameter('fn', '%' . strtolower($data['firstName']) . '%');
        }

        if (!empty($data['lastName'])) {
            $qb->andWhere('LOWER(e.lastName) LIKE :ln')
                ->setParameter('ln', '%' . strtolower($data['lastName']) . '%');
        }

        if (!empty($data['position'])) {
            $qb->andWhere('LOWER(e.position) LIKE :pos')
                ->setParameter('pos', '%' . strtolower($data['position']) . '%');
        }

        $paginator = new Paginator($qb);
        $totalItems = count($paginator);
        $totalPages = max(1, ceil($totalItems / $itemsPerPage));

        $qb->setFirstResult($itemsPerPage * ($page - 1))
            ->setMaxResults($itemsPerPage);

        return [
            'items'        => $paginator->getQuery()->getResult(),
            'totalItems'   => $totalItems,
            'totalPages'   => $totalPages,
            'currentPage'  => $page,
            'itemsPerPage' => $itemsPerPage,
        ];
    }

    //    /**
    //     * @return Employee[] Returns an array of Employee objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Employee
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
