<?php

namespace App\Service;

use App\Entity\Team;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class ScoreService
{
    private EntityManagerInterface $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Team|User $entry
     * @return float|int|null
     */
    public function getPlayerOrTeamRatio(Team|User $entry) {
        return $entry->getNbParticipation() == 0 ? null : ( $entry->getNbWin() / $entry->getNbParticipation() ) * 100 ;
    }

}