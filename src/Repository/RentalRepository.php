<?php

namespace App\Repository;

use App\Entity\Rental;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Rental>
 */
class RentalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rental::class);
    }

    public function getAllByFilter(array $data, int $itemsPerPage, int $page): array
    {
        $qb = $this->createQueryBuilder('r');

        if (!empty($data['status'])) {
            $qb->andWhere('LOWER(r.status) LIKE :st')
                ->setParameter('st', '%' . strtolower($data['status']) . '%');
        }

        if (!empty($data['clientId'])) {
            $qb->andWhere('r.client = :cid')
                ->setParameter('cid', $data['clientId']);
        }

        if (!empty($data['vehicleId'])) {
            $qb->andWhere('r.vehicle = :vid')
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
    //     * @return Rental[] Returns an array of Rental objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Rental
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
