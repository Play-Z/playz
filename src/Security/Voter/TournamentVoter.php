<?php

namespace App\Security\Voter;

use App\Entity\Tournament;
use App\Entity\User;
use App\Service\SecurityService;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class TournamentVoter extends Voter
{
    const VIEW = 'view';

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
        return in_array($attribute, [self::VIEW]);
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

        $targetTournament = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($user);
        }

        return false;
    }

    private function canView(User $user)
    {
        return $this->securityService->isGranted($user,'ROLE_TOURNAMENT_MANAGER');
    }
}