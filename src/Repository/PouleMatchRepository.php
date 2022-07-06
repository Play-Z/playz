<?php

namespace App\Repository;

use App\Entity\PouleMatch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PouleMatch|null find($id, $lockMode = null, $lockVersion = null)
 * @method PouleMatch|null findOneBy(array $criteria, array $orderBy = null)
 * @method PouleMatch[]    findAll()
 * @method PouleMatch[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PouleMatchRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PouleMatch::class);
    }

    // /**
    //  * @return PouleMatch[] Returns an array of PouleMatch objects
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
    public function findOneBySomeField($value): ?PouleMatch
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
