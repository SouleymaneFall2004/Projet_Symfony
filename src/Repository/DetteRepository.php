<?php

namespace App\Repository;

use App\Entity\Dette;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Client;

/**
 * @extends ServiceEntityRepository<Dette>
 */
class DetteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dette::class);
    }

    //    /**
    //     * @return Dette[] Returns an array of Dette objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('d.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Dette
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }


    public function searchDetteForClient(Client $client): array
    {
        return $this->createQueryBuilder('d')
            ->where('d.client = :client')
            ->setParameter('client', $client)
            ->getQuery()->getResult();
    }

    public function listerDetteByFilter($filter, Client $client): array
    {
        if ($filter === 'true') {
            return $this->createQueryBuilder('d')
                ->where('d.client = :client')
                ->andWhere('d.montant - d.montantVerser = 0')
                ->setParameter('client', $client)
                ->getQuery()->getResult();
        } else {
            return $this->createQueryBuilder('d')
                ->where('d.client = :client')
                ->andWhere('d.montant - d.montantVerser > 0')
                ->setParameter('client', $client)
                ->getQuery()->getResult();
        }
    }

    public function listerDetteByFilter1($filter): array
    {
        if ($filter) {
            return $this->createQueryBuilder('d')

                ->Where('d.montant - d.montantVerser = 0')
                ->getQuery()->getResult();
        } else {
            return $this->createQueryBuilder('d')

                ->Where('d.montant - d.montantVerser > 0')
                ->getQuery()->getResult();
        }
    }

    public function findDetteClient(Client $client): array
    {
        return $this->createQueryBuilder('d')
            ->where('d.client = :client')
            ->setParameter('client', $client)
            ->getQuery()->getResult();
    }

    public function findAll(): array
    {
        return $this->createQueryBuilder('d')
            ->getQuery()->getResult();
    }
    // public function filterByEtat($value): array
    // {
    //     if ($value == "en_cours") {
    //         return $this->createQueryBuilder('d')
    //     }
    // }
}
