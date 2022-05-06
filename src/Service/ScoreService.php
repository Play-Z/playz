<?php

namespace App\Service;

use App\Entity\Team;
use App\Entity\Tournament;
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

    public function getEloAvg(Tournament $tournament) {
        $teams = $tournament->getEquipes()->getValues() ;

       $elos = [] ;
        foreach ($teams as $team) {
            $elos[$team->getTeam()->getName()]["team_elo"] = $team->getTeam()->getElo();
            foreach ($team->getTeam()->getUsers() as $player) {
                $elos[$team->getTeam()->getName()][] = $player->getElo() ;
            }
        }
        dd($elos) ;
    }

}