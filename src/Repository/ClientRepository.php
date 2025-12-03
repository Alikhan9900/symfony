<?php

namespace App\Repository;

use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Client>
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    public function getAllByFilter(array $data, int $itemsPerPage, int $page): array
    {
        $qb = $this->createQueryBuilder('c');

        if (!empty($data['firstName'])) {
            $qb->andWhere('LOWER(c.firstName) LIKE :fn')
                ->setParameter('fn', '%' . strtolower($data['firstName']) . '%');
        }

        if (!empty($data['lastName'])) {
            $qb->andWhere('LOWER(c.lastName) LIKE :ln')
                ->setParameter('ln', '%' . strtolower($data['lastName']) . '%');
        }

        if (!empty($data['email'])) {
            $qb->andWhere('LOWER(c.email) LIKE :em')
                ->setParameter('em', '%' . strtolower($data['email']) . '%');
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
    //     * @return Client[] Returns an array of Client objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Client
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
