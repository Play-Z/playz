<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\UserRelation;
use App\Repository\TeamRepository;
use App\Repository\UserRelationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class UserRelationService {

    private UserRelationRepository $userRelationRepository;
    private TeamRepository $teamRepository;
    private Security $security;
    private SecurityService $securityService;
    private EntityManagerInterface $entityManager;

    /**
     * @param UserRelationRepository $userRelationRepository
     * @param TeamRepository $teamRepository
     * @param Security $security
     * @param SecurityService $securityService
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(UserRelationRepository $userRelationRepository, TeamRepository $teamRepository, Security $security, SecurityService $securityService, EntityManagerInterface $entityManager)
    {
        $this->userRelationRepository = $userRelationRepository;
        $this->teamRepository = $teamRepository;
        $this->security = $security;
        $this->securityService = $securityService;
        $this->entityManager = $entityManager;
    }

    public function handleNewFriendRelation($sender, $recipient): string
    {
        $allRelationsOfUser = $this->userRelationRepository->findAllRelationByUserAndType($sender, $recipient, 'friend');

        if (!empty($this->userRelationRepository->findBy(['sender' => $sender]))) {
            $senderRelation = $this->userRelationRepository->findBy(['sender' => $sender, 'type' => 'friend'])[0];
        }

        foreach ($allRelationsOfUser as $relation) {
            if ($relation->getStatus() == 'pending') {
                if ($relation->getSender() == $sender) {
                    return "Vous avez déjà une demande d'ami en attente pour cet utilisateur.";
                }
                elseif ($relation->getRecipient() == $sender) {
                    return "Vous avez déjà une demande d'ami en attente de cet utilisateur.";
                }
            }
            elseif ($relation->getStatus() == 'accepted') {
                return "Vous êtes déjà ami avec cet utilisateur.";
            }
        }

        if (isset($senderRelation) && $senderRelation != null && $senderRelation->getStatus() === 'rejected') {
            $senderRelation->setStatus('pending');
            $this->userRelationRepository->add($senderRelation);
            return "Votre demande d'ami a bien été envoyé";
        }

        $userRelation = (new UserRelation())
            ->setSender($sender)
            ->setRecipient($recipient)
            ->setType('friend');

        $this->userRelationRepository->add($userRelation);

        return "Votre demande d'ami a bien été envoyé";
    }

    public function handleNewTeamRelation($sender, $recipient): string
    {
        if (!is_null($recipient->getTeam())){
            return "Cet utilisateur a déjà une équipe.";
        }

        $allTeamRequestOfUser = $this->userRelationRepository->findAllRelationWhereUserIsRecipientByType($recipient, 'team');

        if (!empty($this->userRelationRepository->findBy(['sender' => $sender]))) {
            $senderRelation = $this->userRelationRepository->findBy(['sender' => $sender, 'type' => 'team']);
        }

        foreach ($allTeamRequestOfUser as $relation) {
            if ($relation->getStatus() == 'pending') {
                if ($relation->getSender() == $sender) {
                    return "Vous avez déjà une invitation d'équipe en attente pour cet utilisateur.";
                }
            }
            elseif ($relation->getStatus() == 'accepted') {
                return "Cet utilisateur a déjà rejoint votre équipe";
            }
        }

        if (isset($senderRelation) && $senderRelation != null && $senderRelation->getStatus() === 'rejected') {
            $this->userRelationRepository->add($senderRelation);
            return "Votre invitation d'équipe a bien été envoyé";
        }

        $userRelation = (new UserRelation())
            ->setSender($sender)
            ->setRecipient($recipient)
            ->setType('team');

        $this->userRelationRepository->add($userRelation);

        return "Votre invitation d'équipe a bien été envoyé";
    }

    public function deleteAllTeamUserRelation($team){
        $userRelation = $this->userRelationRepository->findAllTeamUserRelationByTeam($team);
        foreach ($userRelation as $relation){
            $this->userRelationRepository->remove($relation);
        }
    }

    public function deleteTeamUserRelation($team, $user)
    {
        $userRelation = $this->userRelationRepository->findTeamUserRelationByTeamAndRecipient($team, $user);
        foreach ($userRelation as $relation){
            $this->userRelationRepository->remove($relation);
        }
    }

    public function handleBlock(User $user)
    {
        $sender = $this->security->getUser();
        $recipient = $user;
        $blockedRelation = $this->userRelationRepository->findBlockedRelationByUser($sender, $recipient);

        if (empty($blockedRelation))
        {
            if ($sender->getTeam() === $recipient->getTeam()){
                if ($this->securityService->isGranted($sender, 'ROLE_TEAM_CREATOR')){
                    $team = $sender->getTeam();
                    if (!in_array('ROLE_ADMIN',$user->getRoles())) {
                        $recipient->setRoles(['ROLE_USER']);
                    }
                    $this->deleteTeamUserRelation($team, $recipient);
                    $team->removeUser($recipient);
                    $this->entityManager->flush();

                    return "L'utilisateur a bien été bloqué et virer de votre équipe";
                }
                elseif ($this->securityService->isGranted($recipient, 'ROLE_TEAM_CREATOR')){
                    $team = $recipient->getTeam();
                    if (!in_array('ROLE_ADMIN',$user->getRoles())) {
                        $sender->setRoles(['ROLE_USER']);
                    }
                    $this->deleteTeamUserRelation($sender, $user);
                    $team->removeUser($sender);
                    $this->entityManager->flush();

                    return "L'utilisateur a bien été bloqué et vous avez quittez son équipe";
                }
            }
            $userRelation = (new UserRelation())
                ->setSender($sender)
                ->setRecipient($recipient)
                ->setStatus('blocked');

            $this->userRelationRepository->add($userRelation);
            return "L'utilisateur a bien été bloqué";
        }
        elseif($blockedRelation[0]->getSender() === $sender){
            return "Vous avez déjà bloquer cet utilisateur";
        }
        elseif ($blockedRelation[0]->getRecipient() === $sender){
            return "Cet utilisateur vous a déjà bloquer";
        }
    }
}