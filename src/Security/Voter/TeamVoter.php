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
    const VIEW = 'view';
    const DELETE = 'delete';

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE]) && $subject instanceof \App\Entity\Team;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        $targetTeam = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($targetTeam, $user);
            case self::EDIT:
                return $this->canEdit($targetTeam, $user);
            case self::DELETE:
                return $this->canDelete($targetTeam, $user);
        }

        return false;
    }

    private function canView(Team $targetTeam, User $user)
    {
        return $this->canEdit($targetTeam, $user);
    }

    private function canEdit(Team $targetUser, User $user)
    {
        return in_array('ROLE_ADMIN', $user->getRoles()) || $user->getTeam() === $targetUser;
    }

    private function canDelete(Team $targetUser, User $user){
        return in_array('ROLE_ADMIN', $user->getRoles()) || $user->getTeam() === $targetUser;
    }
}