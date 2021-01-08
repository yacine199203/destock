<?php

namespace App\Repository;

use App\Entity\JobProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method JobProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method JobProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method JobProduct[]    findAll()
 * @method JobProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JobProduct::class);
    }

    // /**
    //  * @return JobProduct[] Returns an array of JobProduct objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('j.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?JobProduct
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
