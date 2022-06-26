<?php

namespace App\Repository;

use App\Entity\PouleEquipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PouleEquipe|null find($id, $lockMode = null, $lockVersion = null)
 * @method PouleEquipe|null findOneBy(array $criteria, array $orderBy = null)
 * @method PouleEquipe[]    findAll()
 * @method PouleEquipe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PouleEquipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PouleEquipe::class);
    }

    // /**
    //  * @return PouleEquipe[] Returns an array of PouleEquipe objects
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
    public function findOneBySomeField($value): ?PouleEquipe
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
