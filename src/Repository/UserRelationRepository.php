<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserRelation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserRelation|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserRelation|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserRelation[]    findAll()
 * @method UserRelation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRelationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserRelation::class);
    }

    /**
     * @return User[] Returns an array of User objects
     */
    public function findAllFriends($user)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            '
                SELECT ur, s, r
                FROM App\Entity\UserRelation ur
                INNER JOIN ur.recipient s
                INNER JOIN ur.sender r 
                WHERE(s.id = :userId AND ur.status = :accepted) OR (r.id = :userId AND ur.status = :accepted)
                '
        )->setParameters(['userId' => $user, 'accepted' => 'accepted']);

        return $query->getResult();

//        return $this->createQueryBuilder('ru')
//            ->select(array('u'))
//            ->from(UserRelation::class,'u')
//            ->innerJoin('u.sender', 's', 'WITH', 's = u.sender')
//            ->innerJoin('u.recipient', 'r', 'WITH', 'r = u.recipient')
//            ->andWhere('u.sender = :user OR u.recipient = :user')
//            ->andWhere('u.status = :accepted')
//            ->setParameters(['user' => $user, 'accepted' => 'accepted'])
//            ->getQuery()
//            ->getResult()
//            ;
    }

    /**
     * @return UserRelation[] Returns an array of UserRelation objects
     */
    public function findAllRelationByUser($sender, $recipient)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.sender = :sender AND u.recipient = :recipient')
            ->orWhere('u.sender = :recipient AND u.recipient = :sender')
            ->setParameters(['sender' => $sender, 'recipient' => $recipient])
            ->getQuery()
            ->getResult()
            ;
    }
}
