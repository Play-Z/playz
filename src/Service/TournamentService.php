<?php

namespace App\Service;

use App\Entity\Tournament;
use App\Entity\TournamentMatch;
use Doctrine\ORM\EntityManagerInterface;

class TournamentService
{

    private EntityManagerInterface $entityManager;


    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createMatchesForATournament(Int $max_team, Tournament $tournament,  Bool $isCallFromrecursive ,  $parents_matchs = null )
    {
        if($isCallFromrecursive == false) {
            $total_match =  $max_team   - 2 ;


            $finalMatch = new TournamentMatch('Finale',$tournament);
            $this->entityManager->persist($finalMatch);

            $demi1 = new TournamentMatch('Demi-finale',$tournament, $finalMatch);
            $demi2 = new TournamentMatch('Demi-finale',$tournament,$finalMatch);
            $this->entityManager->persist($demi1);
            $this->entityManager->persist($demi2);

            $this->entityManager->flush();
            if($total_match - 2 > 0)
                $this->createMatchesForATournament($total_match-2,$tournament,true ,array(
                    $demi1,
                    $demi2
                )) ;
        } else {
            $matches = [] ;

            foreach($parents_matchs as $match) {
                $matches[] = new TournamentMatch('',$tournament,$match) ;
                $matches[] = new TournamentMatch('',$tournament,$match) ;
            }
            foreach ($matches as $match){
                $this->entityManager->persist($match);
            }
            $this->entityManager->flush();

            if($max_team - count($matches)  > 0 )
                $this->createMatchesForATournament($max_team - (count($matches) ),$tournament,true,$matches) ;
        }

    }

    public function startATournament(Tournament $tournament) {
        $matches = [] ;
        $teams = $tournament->getEquipes()->getValues() ;
        foreach ($tournament->getTournamentMatches() as $key => $match) {

            if(count($match->getMatchEnfants()->getValues() )=== 0) {
                array_push($matches,$match) ;
            }
        }

        foreach ($matches as  $key => $match) {

            $match->setTeamOne($teams[$key*2]);
            $match->setTeamTwo($teams[$key*2 + 1]);

            $this->entityManager->persist($match);
        }


        $tournament->setStatus(true) ;
        $this->entityManager->persist($tournament);
        $this->entityManager->flush();
    }
}