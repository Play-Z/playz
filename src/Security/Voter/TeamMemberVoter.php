<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Service\SecurityService;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class TeamMemberVoter extends Voter
{
    const MANAGE = 'manage';
    const FIRE = 'fire';

    private Security $security;
    private SecurityService $securityService;

    public function __construct(Security $security, SecurityService $securityService)
    {
        $this->security = $security;
        $this->securityService = $securityService;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::MANAGE, self::FIRE]) && $subject instanceof \App\Entity\User;
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
            case self::MANAGE:
                return $this->canManage($targetUser, $user);
            case self::FIRE:
                return $this->canFire($targetUser, $user);
        }

        return false;
    }

    private function canManage(User $targetUser, User $user)
    {
        if ($this->securityService->isGranted($targetUser,'ROLE_TEAM_CREATOR')){
            return false;
        }
        else{
            return $this->securityService->isGranted($user, 'ROLE_TEAM_MANAGER') && $targetUser !== $user;
        }
    }

    private function canFire(User $targetUser, User $user)
    {
        return $this->security->isGranted('ROLE_TEAM_CREATOR') && $targetUser !== $user && $user->getTeam() === $targetUser->getTeam();
    }
}