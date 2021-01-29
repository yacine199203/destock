<?php

namespace App\Repository;

use App\Entity\RealisationImages;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RealisationImages|null find($id, $lockMode = null, $lockVersion = null)
 * @method RealisationImages|null findOneBy(array $criteria, array $orderBy = null)
 * @method RealisationImages[]    findAll()
 * @method RealisationImages[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RealisationImagesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RealisationImages::class);
    }

    // /**
    //  * @return RealisationImages[] Returns an array of RealisationImages objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RealisationImages
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
