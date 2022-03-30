<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Service\SecurityService;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class UserProfileVoter extends Voter
{
    const EDIT = 'edit';
    const VIEW = 'view';
    const INSCRIPTION = 'inscription' ;

    private $security;
    private SecurityService $securityService;


    public function __construct(Security $security, SecurityService $securityService)
    {
        $this->security = $security;
        $this->securityService = $securityService;
    }

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW, self::INSCRIPTION]) && $subject instanceof \App\Entity\User;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }


        $targetUser = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($targetUser, $user);
            case self::EDIT:
                return $this->canEdit($targetUser, $user);
            case self::INSCRIPTION :
                return $this->canRegister( $user) ;

        }

        return false;
    }

    private function canView(User $targetUser, User $user)
    {
        return $this->canEdit($targetUser, $user);
    }

    private function canEdit(User $targetUser, User $user)
    {
        return $user === $targetUser;
    }

    private function canRegister(User $user)
    {
        return $this->securityService->isGranted($user,'ROLE_TEAM_CREATOR');
    }
}