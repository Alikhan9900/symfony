<?php

namespace App\Repository;

use App\Entity\Address;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Address>
 */
class AddressRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Address::class);
    }

    public function getAllByFilter(array $data, int $itemsPerPage, int $page): array
    {
        if ($itemsPerPage <= 0) {
            $itemsPerPage = 10;
        }
        if ($page <= 0) {
            $page = 1;
        }

        $qb = $this->createQueryBuilder('a');

        if (!empty($data['country'])) {
            $qb->andWhere('LOWER(a.country) LIKE :country')
                ->setParameter('country', '%' . mb_strtolower($data['country']) . '%');
        }

        if (!empty($data['city'])) {
            $qb->andWhere('LOWER(a.city) LIKE :city')
                ->setParameter('city', '%' . mb_strtolower($data['city']) . '%');
        }

        if (!empty($data['zip'])) {
            $qb->andWhere('a.zip = :zip')
                ->setParameter('zip', $data['zip']);
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
    //     * @return Address[] Returns an array of Address objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Address
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
