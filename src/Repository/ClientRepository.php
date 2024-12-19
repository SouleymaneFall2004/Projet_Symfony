<?php

namespace App\Repository;

use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @extends ServiceEntityRepository<Client>
 */
class ClientRepository extends ServiceEntityRepository 
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
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

    public function  searchByTelephoneAndName(String $search): array
    {
       
        return $this->createQueryBuilder('c')
        ->where('c.surname LIKE :search')
        ->orWhere('c.telephone LIKE :search')
        ->setParameter('search', '%' . $search . '%')
        ->getQuery()->getResult();
    }

    public function findAll(): array
    {
        return $this->createQueryBuilder('c')
            ->getQuery()->getResult();
    }

    public function findAccountentClient($filter): array
    {
        if($filter ==='true'){
            return $this->createQueryBuilder('c')
            ->where('c.users IS NOT NULL')
            ->getQuery()->getResult();
        }
        else{
            return $this->createQueryBuilder('c')
            ->where('c.users IS NULL')
            ->getQuery()->getResult();  
        }
    }

  

}
