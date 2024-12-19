<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    //    /**
    //     * @return Article[] Returns an array of Article objects
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
    public function findAll(): array
    {
        return $this->createQueryBuilder('c')
            ->getQuery()->getResult();
    }

    public function findByDisponible(bool $value): array
    {
        if ($value) {
            return $this->createQueryBuilder('c')
                ->andWhere('c.qteStock > 0')

                ->getQuery()->getResult();
        } else {
            return $this->createQueryBuilder('c')
                ->andWhere('c.qteStock =0')
                ->getQuery()->getResult();
        }
    }
    public function findById($id)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.id = :val')
            ->setParameter('val', $id)
            ->getQuery()->getResult();
    }

    public function findByLibelle(string $value): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.libelle = :val')
            ->setParameter('val', $value)
            ->getQuery()->getResult();
    }
    public function findByanything(string $value, bool $value2): array
    {
        if ($value2) {
            return $this->createQueryBuilder('c')
                ->andWhere('Lower(c.libelle) = :val')
                ->setParameter('val', strtolower($value))
                ->andWhere('c.qteStock > 0')
                ->getQuery()->getResult();
        } else {
            return $this->createQueryBuilder('c')
                ->andWhere('Lower(c.libelle) = :val')
                ->setParameter('val', strtolower($value))
                ->andWhere('c.qteStock = 0')
                ->getQuery()->getResult();
        }
    }
}
