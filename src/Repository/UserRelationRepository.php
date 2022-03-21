<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserRelation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
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
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(UserRelation $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(UserRelation $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
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
                INNER JOIN ur.recipient r
                INNER JOIN ur.sender s
                WHERE(s.id = :userId AND ur.status = :accepted) OR (r.id = :userId AND ur.status = :accepted)
                '
        )->setParameters(['userId' => $user, 'accepted' => 'accepted']);

        return $query->getResult();
    }

    /**
     * @return User[] Returns an array of User objects
     */
    public function findUserSenderTeam($id)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            '
                SELECT ur, s, r, t
                FROM App\Entity\UserRelation ur
                INNER JOIN ur.recipient r
                INNER JOIN ur.sender s
                INNER JOIN s.team t
                WHERE ur.id = :id
                '
        )->setParameters(['id'=> $id]);

        return $query->getResult();
    }

//    /**
//     * @return UserRelation[] Returns an array of UserRelation objects
//     */
//    public function findAllRelationByUser($sender, $recipient)
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.sender = :sender AND u.recipient = :recipient')
//            ->orWhere('u.sender = :recipient AND u.recipient = :sender')
//            ->setParameters(['sender' => $sender, 'recipient' => $recipient])
//            ->getQuery()
//            ->getResult()
//            ;
//    }

    /**
     * @return UserRelation[] Returns an array of UserRelation objects
     */
    public function findBlockedRelationByUser($sender, $recipient)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.sender = :sender AND u.recipient = :recipient AND u.status = :status')
            ->setParameters(['sender' => $sender, 'recipient' => $recipient, 'status' => 'blocked'])
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return UserRelation[] Returns an array of UserRelation objects
     */
    public function findAllRelationByUserAndType($sender, $recipient, $relationType)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.sender = :sender AND u.recipient = :recipient AND u.type = :type')
            ->orWhere('u.sender = :recipient AND u.recipient = :sender AND u.type = :type')
            ->setParameters(['sender' => $sender, 'recipient' => $recipient, 'type' => $relationType])
            ->getQuery()
            ->getResult()
            ;
    }
}
