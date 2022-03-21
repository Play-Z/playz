<?php

namespace App\Service;

use App\Entity\UserRelation;
use App\Repository\UserRelationRepository;

class UserRelationService {

    private UserRelationRepository $userRelationRepository;

    /**
     * @param UserRelationRepository $userRelationRepository
     */
    public function __construct(UserRelationRepository $userRelationRepository)
    {
        $this->userRelationRepository = $userRelationRepository;
    }

    public function handleNewFriendRelation($sender, $recipient): string
    {
        $allRelationsOfUser = $this->userRelationRepository->findAllRelationByUserAndType($sender, $recipient, 'friend');

        if (!empty($this->userRelationRepository->findBy(['sender' => $sender]))) {
            $senderRelation = $this->userRelationRepository->findBy(['sender' => $sender])[0];
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
        $allRelationsOfUser = $this->userRelationRepository->findAllRelationByUserAndType($sender, $recipient, 'team');

        if (!empty($this->userRelationRepository->findBy(['sender' => $sender]))) {
            $senderRelation = $this->userRelationRepository->findBy(['sender' => $sender])[0];
        }

        foreach ($allRelationsOfUser as $relation) {
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
}