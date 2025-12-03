<?php

namespace App\Repository;

use App\Entity\MaintenanceRecord;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<MaintenanceRecord>
 */
class MaintenanceRecordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MaintenanceRecord::class);
    }

    public function getAllByFilter(array $data, int $itemsPerPage, int $page): array
    {
        $qb = $this->createQueryBuilder('m');

        if (!empty($data['type'])) {
            $qb->andWhere('LOWER(m.type) LIKE :t')
                ->setParameter('t', '%' . strtolower($data['type']) . '%');
        }

        if (!empty($data['vehicleId'])) {
            $qb->andWhere('m.vehicle = :vid')
                ->setParameter('vid', $data['vehicleId']);
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
    //     * @return MaintenanceRecord[] Returns an array of MaintenanceRecord objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?MaintenanceRecord
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
