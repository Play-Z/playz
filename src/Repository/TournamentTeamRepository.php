<?php

namespace App\Repository;

use App\Entity\TournamentTeam;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TournamentTeam|null find($id, $lockMode = null, $lockVersion = null)
 * @method TournamentTeam|null findOneBy(array $criteria, array $orderBy = null)
 * @method TournamentTeam[]    findAll()
 * @method TournamentTeam[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TournamentTeamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TournamentTeam::class);
    }

    // /**
    //  * @return TournamentTeam[] Returns an array of TournamentTeam objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TournamentTeam
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
