<?php

namespace App\Repository;

use App\Entity\VehicleModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<VehicleModel>
 */
class VehicleModelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VehicleModel::class);
    }

    public function getAllByFilter(array $data, int $itemsPerPage, int $page): array
    {
        if ($itemsPerPage <= 0) {
            $itemsPerPage = 10;
        }
        if ($page <= 0) {
            $page = 1;
        }

        $qb = $this->createQueryBuilder('m');

        if (!empty($data['name'])) {
            $qb->andWhere('LOWER(m.name) LIKE :name')
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
    //     * @return VehicleModel[] Returns an array of VehicleModel objects
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

    //    public function findOneBySomeField($value): ?VehicleModel
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
