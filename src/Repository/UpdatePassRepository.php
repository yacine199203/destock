<?php

namespace App\Repository;

use App\Entity\UpdatePass;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UpdatePass|null find($id, $lockMode = null, $lockVersion = null)
 * @method UpdatePass|null findOneBy(array $criteria, array $orderBy = null)
 * @method UpdatePass[]    findAll()
 * @method UpdatePass[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UpdatePassRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UpdatePass::class);
    }

    // /**
    //  * @return UpdatePass[] Returns an array of UpdatePass objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UpdatePass
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
