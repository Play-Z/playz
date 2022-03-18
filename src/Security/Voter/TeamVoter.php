<?php

namespace App\Security\Voter;

use App\Entity\Team;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class TeamVoter extends Voter
{
    const EDIT = 'edit';
    const DELETE = 'delete';

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::DELETE]) && $subject instanceof \App\Entity\Team;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        $targetTeam = $subject;

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($targetTeam, $user);
            case self::DELETE:
                return $this->canDelete($targetTeam, $user);
        }

        return false;
    }

    private function canEdit(Team $targetTeam, User $user)
    {
        return in_array('ROLE_ADMIN', $user->getRoles()) || $targetTeam->getCreatedBy() === $user && in_array('ROLE_TEAM_CREATOR', $user->getRoles());
    }

    private function canDelete(Team $targetTeam, User $user){
        return in_array('ROLE_ADMIN', $user->getRoles()) || $targetTeam->getCreatedBy() === $user && in_array('ROLE_TEAM_CREATOR', $user->getRoles());
    }
}