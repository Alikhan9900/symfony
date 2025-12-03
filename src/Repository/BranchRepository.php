<?php

namespace App\Repository;

use App\Entity\Branch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Branch>
 */
class BranchRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Branch::class);
    }

    public function getAllByFilter(array $data, int $itemsPerPage, int $page): array
    {
        if ($itemsPerPage <= 0) {
            $itemsPerPage = 10;
        }
        if ($page <= 0) {
            $page = 1;
        }

        $qb = $this->createQueryBuilder('b');

        if (!empty($data['name'])) {
            $qb->andWhere('LOWER(b.name) LIKE :name')
                ->setParameter('name', '%' . mb_strtolower($data['name']) . '%');
        }

        $paginator = new Paginator($qb);
        $totalItems = count($paginator);
        $totalPages = $totalItems > 0 ? (int)ceil($totalItems / $itemsPerPage) : 1;

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
    //     * @return Branch[] Returns an array of Branch objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Branch
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
