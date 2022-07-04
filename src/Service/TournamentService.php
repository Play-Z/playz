<?php

namespace App\Service;

use App\Entity\PouleMatch;
use App\Entity\Tournament;
use App\Entity\Poule;
use App\Entity\TournamentMatch;
use App\Entity\PouleEquipe;
use App\Entity\TournamentTeam;
use App\Repository\PouleRepository;
use App\Repository\TournamentMatchRepository;
use App\Repository\TournamentRepository;
use Doctrine\ORM\EntityManagerInterface;

class TournamentService
{

    private EntityManagerInterface $entityManager;
    private TournamentMatchRepository $tournamentMatchRepository ;
    private PouleRepository $pouleRepository ;


    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, TournamentMatchRepository $tournamentMatchRepository, PouleRepository $pouleRepository)
    {
        $this->entityManager = $entityManager;
        $this->tournamentMatchRepository = $tournamentMatchRepository ;
        $this->pouleRepository = $pouleRepository ;
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

    public function createPoule
    (Int $max_team, Tournament $tournament,  Bool $isCallFromrecursive , $etape = 0,  $parents_matchs = null )
    {
            $nbPoules = $max_team / 4;
            $total_match =  6 * $nbPoules;
            $etape = log($max_team,2);

            /*Création des poules*/
            for ($i = 1;$i <= $nbPoules; $i++){
                $poule = new Poule;
                $poule->setName('poule '.$i);
                $tournament->addPoule($poule);
                $this->entityManager->persist($tournament);
            }
            $this->entityManager->flush();
    }

    /** @var Tournament\ $tournament*/
    public function createTournamentChild(Tournament $tournament, $equipes)
    {
        /* Create base data for Tournament */
        $tournamentChild = new Tournament();
        $tournamentChild->setName($tournament->getName().' - SubTournament');
//        $tournamentChild->setParent($tournament);

        /* get team of parent Tournament */
        dump($tournament->getPoules());

        exit();
        $tournamentChild->setMaxTeam($tournament->getMaxTeam() / 2);

        /* Create tournament child matches */
        $this->createMatchesForATournament($tournamentChild->getMaxTeam(),$tournamentChild,false);
        $this->startATournament($tournamentChild);

        $tournament->addChild();
    }

    public function startATournament(Tournament $tournament) {
        $matches = $this->tournamentMatchRepository->findBy([
            'name'=>1,
            'tournaments'=>$tournament
        ]);

        $teams = $tournament->getEquipes()->getValues() ;
        foreach ($teams as $team) {
            $team->getTeam()->setNbParticipation($team->getTeam()->getNbParticipation()+1);
            foreach ($team->getTeam()->getUsers() as $user) {
                $user->setNbParticipation($user->getNbParticipation()+1);
            }
        }

        foreach ($matches as  $key => $match) {

            $match->setTeamOne($teams[$key*2]);
            $match->setTeamTwo($teams[$key*2 + 1]);

            $this->entityManager->persist($match);
        }


        $tournament->setStatus(true);
        $this->entityManager->persist($tournament);
        $this->entityManager->flush();
    }

    public function startAPouleTournament(Tournament $tournament) {
        /* Create poule match, all teams confront themself */

        $poules = $tournament->getPoules();
        $teams = $tournament->getEquipes()->getValues();
        foreach ($poules as $poule){
            /* add team to poule, there can only be 4 team in a poule */
            for ($i = 1; $i <= 4; $i++){
                $randomizedTeam = array_rand($teams,1);
                $pouleEquipe = new PouleEquipe;
                $pouleEquipe->setTournamentTeam($teams[$randomizedTeam]);
                $pouleEquipe->setPoule($poule);
                $poule->addPouleEquipe($pouleEquipe);
                unset($teams[$randomizedTeam]);
            }

//            /* Create 6 match for every poule */
            $teamsValues = $poule->getPouleEquipes()->getValues();

            /*j1 vs j2*/
            $match1 = new PouleMatch();
            $match1->setPoule($poule);
            $match1->setEquipeUne($teamsValues[0]);
            $match1->setEquipeDeux($teamsValues[1]);
            $poule->addPouleMatch($match1);

            /*j3 vs j4*/
            $match2 = new PouleMatch();
            $match2->setPoule($poule);
            $match2->setEquipeUne($teamsValues[2]);
            $match2->setEquipeDeux($teamsValues[3]);
            $poule->addPouleMatch($match2);

            /*j2 vs j3*/
            $match3 = new PouleMatch();
            $match3->setPoule($poule);
            $match3->setEquipeUne($teamsValues[1]);
            $match3->setEquipeDeux($teamsValues[2]);
            $poule->addPouleMatch($match3);

            /*j1 vs j4*/
            $match4 = new PouleMatch();
            $match4->setPoule($poule);
            $match4->setEquipeUne($teamsValues[0]);
            $match4->setEquipeDeux($teamsValues[3]);
            $poule->addPouleMatch($match4);

            /*j1 vs j3*/
            $match5 = new PouleMatch();
            $match5->setPoule($poule);
            $match5->setEquipeUne($teamsValues[0]);
            $match5->setEquipeDeux($teamsValues[2]);
            $poule->addPouleMatch($match5);

            /*j2 vs j4*/
            $match6 = new PouleMatch();
            $match6->setPoule($poule);
            $match6->setEquipeUne($teamsValues[1]);
            $match6->setEquipeDeux($teamsValues[3]);
            $poule->addPouleMatch($match6);
        }
        $tournament->setStatus(true);
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


    public function getPoulesMatchs($tournaments): array
    {
        $poules = $this->pouleRepository->findBy(['tournament' => $tournaments]) ;
        return $poules;
    }

   public function removePlayersOfTournamentTeam(TournamentTeam $team) {

       //Removing players from a TournamentTeam
       foreach ($team->getPlayers() as $player) {
           $player->removeTournamentTeams($team);
           $this->entityManager->persist($player);
           var_dump("une team a été vidé de ses joueurs");
       }
       $this->entityManager->flush();
   }
}