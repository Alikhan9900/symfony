<?php

namespace App\Repository;

use App\Entity\Vehicle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;


/**
 * @extends ServiceEntityRepository<Vehicle>
 */
class VehicleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vehicle::class);
    }

    public function getAllByFilter(array $data, int $itemsPerPage, int $page): array
    {
        if ($itemsPerPage <= 0) {
            $itemsPerPage = 10;
        }
        if ($page <= 0) {
            $page = 1;
        }

        $qb = $this->createQueryBuilder('v');

        if (!empty($data['vin'])) {
            $qb->andWhere('v.vin LIKE :vin')
                ->setParameter('vin', '%' . $data['vin'] . '%');
        }

        if (!empty($data['status'])) {
            $qb->andWhere('v.status = :status')
                ->setParameter('status', $data['status']);
        }

        if (!empty($data['year'])) {
            $qb->andWhere('v.year = :year')
                ->setParameter('year', (int)$data['year']);
        }

        if (!empty($data['modelId'])) {
            $qb->andWhere('v.model = :modelId')
                ->setParameter('modelId', (int)$data['modelId']);
        }

        if (!empty($data['branchId'])) {
            $qb->andWhere('v.branch = :branchId')
                ->setParameter('branchId', (int)$data['branchId']);
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
    //     * @return Vehicle[] Returns an array of Vehicle objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('v.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Vehicle
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
