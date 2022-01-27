<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class TeamVoter extends Voter
{
    const EDIT = 'edit';
    const VIEW = 'view';

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW]) && $subject instanceof \App\Entity\Team;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case 'edit':
                return in_array('ROLE_ADMIN', $user->getRoles()) || $subject->getCreatedBy() === $user;
                break;
            case 'view':
                // logic to determine if the user can VIEW
                // return true or false
                break;
        }

        return false;
    }
}