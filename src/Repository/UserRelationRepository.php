<?php

namespace App\Repository;

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
     * @return UserRelation[] Returns an array of UserRelation objects
     */
    public function findAllFriends($user)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            '
                SELECT us
                FROM App\Entity\UserRelation r
                INNER JOIN r.sender us
                WHERE r.status = :accepted AND r.sender = :user OR r.recipient = :user
                '
        )->setParameters(['user' => $user, 'accepted' => 'accepted']);

        return $query->getResult();
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
