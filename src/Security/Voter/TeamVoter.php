<?php

namespace App\Security\Voter;

use App\Entity\Team;
use App\Entity\User;
use App\Service\SecurityService;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class TeamVoter extends Voter
{
    const EDIT = 'edit';
    const LEAVE = 'leave';
    const DELETE = 'delete';

    private $security;
    private SecurityService $securityService;


    public function __construct(Security $security, SecurityService $securityService)
    {
        $this->security = $security;
        $this->securityService = $securityService;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::LEAVE, self::DELETE]) && $subject instanceof \App\Entity\Team;
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

        $targetTeam = $subject;

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($targetTeam, $user);
            case self::LEAVE:
                return $this->canLeave($targetTeam, $user);
            case self::DELETE:
                return $this->canDelete($targetTeam, $user);
        }

        return false;
    }

    private function canEdit(Team $targetTeam, User $user)
    {
        return $targetTeam->getCreatedBy() === $user || $this->securityService->isGranted($user,'ROLE_TEAM_CREATOR');
    }

    private function canLeave(Team $targetTeam, User $user)
    {
        return $user->getTeam() === $targetTeam;
    }

    private function canDelete(Team $targetTeam, User $user){
        return $targetTeam->getCreatedBy() === $user || $this->securityService->isGranted($user,'ROLE_TEAM_CREATOR');
    }
}