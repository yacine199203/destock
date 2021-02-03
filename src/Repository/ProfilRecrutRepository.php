<?php

namespace App\Repository;

use App\Entity\ProfilRecrut;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProfilRecrut|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProfilRecrut|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProfilRecrut[]    findAll()
 * @method ProfilRecrut[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProfilRecrutRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProfilRecrut::class);
    }

    // /**
    //  * @return ProfilRecrut[] Returns an array of ProfilRecrut objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ProfilRecrut
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
