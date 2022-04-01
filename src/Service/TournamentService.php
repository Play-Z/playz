<?php

namespace App\Service;

use App\Entity\Tournament;
use App\Entity\TournamentMatch;
use App\Repository\TournamentMatchRepository;
use App\Repository\TournamentRepository;
use Doctrine\ORM\EntityManagerInterface;

class TournamentService
{

    private EntityManagerInterface $entityManager;
    private TournamentMatchRepository $tournamentMatchRepository ;


    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, TournamentMatchRepository $tournamentMatchRepository )
    {
        $this->entityManager = $entityManager;
        $this->tournamentMatchRepository = $tournamentMatchRepository ;
    }

    public function createMatchesForATournament(Int $max_team, Tournament $tournament,  Bool $isCallFromrecursive , $etape = 0,  $parents_matchs = null )
    {
        if($isCallFromrecursive == false) {
            $total_match =  $max_team   - 2 ;
            $etape = log($max_team,2) ;

            $finalMatch = new TournamentMatch($etape,$tournament);
            $this->entityManager->persist($finalMatch);

            $demi1 = new TournamentMatch($etape-1,$tournament, $finalMatch);
            $demi2 = new TournamentMatch($etape-1,$tournament,$finalMatch);
            $this->entityManager->persist($demi1);
            $this->entityManager->persist($demi2);

            $this->entityManager->flush();
            if($total_match - 2 > 0)
                $this->createMatchesForATournament($total_match-2,$tournament,true ,$etape-2,array(
                    $demi1,
                    $demi2
                )) ;
        } else {
            $matches = [] ;

            foreach($parents_matchs as $match) {
                $matches[] = new TournamentMatch($etape,$tournament,$match) ;
                $matches[] = new TournamentMatch($etape,$tournament,$match) ;
            }
            foreach ($matches as $match){

                $this->entityManager->persist($match);
            }
            $this->entityManager->flush();

            if($max_team - count($matches)  > 0 )
                $this->createMatchesForATournament($max_team - (count($matches) ),$tournament,true,$etape-1,$matches) ;
        }

    }

    public function startATournament(Tournament $tournament) {
        $matches = $this->tournamentMatchRepository->findBy([
            'name'=>1,
            'tournaments'=>$tournament
        ]);

        $teams = $tournament->getEquipes()->getValues() ;


        foreach ($matches as  $key => $match) {

            $match->setTeamOne($teams[$key*2]);
            $match->setTeamTwo($teams[$key*2 + 1]);

            $this->entityManager->persist($match);
        }


        $tournament->setStatus(true) ;
        $this->entityManager->persist($tournament);
        $this->entityManager->flush();
    }

   public function getMatchesOfEtape($tournament): array
   {
       $matches = [] ;
       $count = 1 ;
       do {
           $match = $this->tournamentMatchRepository->findBy(['name'=> $count, 'tournaments' => $tournament]) ;
           $isOk = true ;
           foreach ($match as $m) {
               if($m->getStatus() == false)
                   $isOk = false ;
           }

           if(!$isOk) {
               $matches = $match ;
           } else $count++ ;
       }while($matches == null) ;

       return $matches ;
   }
}