<?php

namespace App\Controller\tournament;

use App\Entity\PouleMatch;
use App\Entity\Tournament;
use App\Entity\TournamentMatch;
use App\Form\EditTournamentType;
use App\Form\PouleMatchSetVictoryType;
use App\Form\TournamentMatchSetVictoryType;
use App\Form\TournamentType;
use App\Repository\TournamentMatchRepository;
use App\Repository\PouleRepository;
use App\Repository\TournamentRepository;
use App\Repository\TournamentTeamRepository;
use App\Security\Voter\TournamentVoter;
use App\Service\TournamentService;
use DateTimeZone;
use JetBrains\PhpStorm\Pure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ManagementTournamentController extends AbstractController
{
    #[IsGranted(TournamentVoter::VIEW)]
    #[Route('/', name: 'tournament_dashboard')]
    public function index(TournamentRepository $tournamentRepository): Response
    {
        $user = $this->getUser();
        $tournaments = $tournamentRepository->findBy(['createdBy' => $user]);

        return $this->render('tournament/index.html.twig', [
            'tournaments' => $tournaments,
        ]);
    }

    #[Route('/new', name: 'tournament_new')]
    public function new(Request $request, TournamentService $tournamentService): Response
    {
        $tournament = new Tournament();
        $form = $this->createForm(TournamentType::class, $tournament);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($tournament->getPouleType() === true){
                $tournamentService->createPoule($tournament->getMaxTeamParticipant(), $tournament, false);
            }else{
                $tournamentService->createMatchesForATournament($tournament->getMaxTeamParticipant(), $tournament, false);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tournament);
            $entityManager->flush();
            $this->addFlash('success', 'Votre tournoi a bien été créer !');

            return $this->redirectToRoute('tournament_dashboard', [], Response::HTTP_CREATED);
        }
        return $this->renderForm('tournament/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/edit/{slug}', name: 'tournament_edit')]
    public function edit(Request $request, Tournament $tournament,TournamentService $tournamentService, TournamentRepository $tournamentRepository, PouleRepository $pouleRepository): Response
    {

        if($tournament->getStatus() !== false && $tournament->getStatus() !== true){
            $form = $this->createForm(EditTournamentType::class, $tournament);

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager() ;
                $em->persist($tournament);
                $em->flush();
                $this->addFlash('success','Le tournoi a bien été modifié.');
                return $this->redirectToRoute('tournament_dashboard') ;
            }
            return $this->renderForm('tournament/edit.html.twig', [
                'form' => $form,
                'tournament' => $tournament
            ]);
        } else {
            if($tournament->getStatus() === false) {
                return $this->redirectToRoute('tournament_dashboard') ;
            }
            if ($tournament->getPouleType()) {
                $finaleDisable = true;
                $matches = $tournamentRepository->getAllMatchOfTournament($tournament->getId());
                $allMatches = [];
                foreach ($matches as $match){
                    $allMatches[] = $match['FirstTeamWin'];
                }

                if (!in_array(null,$allMatches, true)){
                    $finaleDisable = false;
                }



                return $this->renderForm('tournament/edit.html.twig', [
                    'poules' => $tournamentService->getPoulesMatchs($tournament),
                    'matches' => null,
                    'tournament' => $tournament,
                    'finaleDisable' => $finaleDisable,
                ]);
            }
            return $this->renderForm('tournament/edit.html.twig', [
                'matches' => $tournamentService->getMatchesOfEtape($tournament),
                'poules' => null,
                'tournament' => $tournament,
                'finaleDisable' => true
            ]);
        }
    }

    #[Route('/edit/{slug}/start_phase_finales', name: 'tournament_start_phase_finale')]
    public function pouleStartPhaseFinale(Tournament $tournament, Request $request, TournamentService $tournamentService, TournamentTeamRepository $tournamentTeamRepository): Response
    {
        $finalistes = [];

        foreach ($request->query->all() as $finalistId){
            $finalistes[] = $tournamentTeamRepository->find($finalistId);
        }
        $tournamentService->createTournamentChild($tournament,$finalistes);

        return $this->redirectToRoute('tournament_edit',[
            'slug' => $tournament->getTournamentChild()->getSlug()
        ]) ;
    }

    #[Route('/start/{slug}', name: 'tournament_start')]
    public function start(Request $request, Tournament $tournament, TournamentService $tournamentService): Response
    {
        if (new \DateTime('',new DateTimeZone('Europe/Paris')) ===  new \DateTime()){
            $date = new \DateTime('',new DateTimeZone('Europe/Paris'));
        }else{
            $date = new \DateTime();
        }
//        if($tournament->getStartAt() <= $date) {
            if(count($tournament->getEquipes()) == $tournament->getMaxTeamParticipant()) {
                if($tournament->getPouleType() === true){
                    $tournamentService->startAPouleTournament($tournament);
                }else{
                    $tournamentService->startATournament($tournament);
                }
                $this->addFlash('success','Le tournoi a bien été lancé');
            } else $this->addFlash('error','Le tournoi n`\'a pas pu être lancé. Il n\'y a pas assez d\'équipes inscrites ');
//        } else $this->addFlash('error','Le tournoi n`\'a pas pu être lancé. La date de début n\'a pas été atteinte');
        return $this->redirectToRoute('tournament_dashboard') ;
    }

    #[Route('/edit/{t_slug}/set-victory/{id}' , name:'tournament_set_victory')]
    public function setVictory(TournamentMatch $tournamentMatch, Request $request) {
        $form = $this->createForm(TournamentMatchSetVictoryType::class,$tournamentMatch) ;
        $form->handleRequest($request) ;
        if($form->isSubmitted() && $form->isValid()  ) {
            $em = $this->getDoctrine()->getManager() ;
            $tournamentMatch->setStatus(true);
            $parentMatch =  $tournamentMatch->getMatchParent();

            if($parentMatch == null) {
                $tournamentMatch->getTournaments()->setStatus(false) ;
                if($tournamentMatch->getTeamOneWin() == true) {

                    $team =  $tournamentMatch->getTeamOne()->getTeam();
                    $team->setNbWin($team->getNbWin() + 1);
                    foreach ($team->getUsers() as $user) {
                        $user->setNbWin($user->getNbWin()+1);
                        $em->persist($user);
                    }
                    $em->persist($team);
                } else
                {
                    $team =  $tournamentMatch->getTeamTwo()->getTeam() ;
                    $team->setNbWin($team->getNbWin() + 1);
                    foreach ($team->getUsers() as $user) {
                        $user->setNbWin($user->getNbWin()+1);
                        $em->persist($user);
                    }
                    $em->persist($team);
                }
            }
            else {
                if($parentMatch->getTeamOne() != null) {
                    if($tournamentMatch->getTeamOneWin() == true)
                        $parentMatch->setTeamTwo($tournamentMatch->getTeamOne());
                    else {
                        $parentMatch->setTeamTwo($tournamentMatch->getTeamTwo());
                    }
                } else {
                    if($tournamentMatch->getTeamOneWin() == true)
                        $parentMatch->setTeamOne($tournamentMatch->getTeamOne());
                    else {
                        $parentMatch->setTeamOne($tournamentMatch->getTeamTwo());
                    }
                }
                $em->persist($parentMatch);
            }


            $em->persist($tournamentMatch);
            $em->flush();

            $this->addFlash('success','Le vainqueur du match a été établi avec succès !');
            return $this->redirectToRoute('tournament_edit',[
                'slug' => $tournamentMatch->getTournaments()->getSlug()
            ]) ;
        }
        return $this->renderForm('tournament/editMatch.html.twig',[
            'match' => $tournamentMatch,
            'form' => $form,
            'TeamOne' => $tournamentMatch->getTeamOne()->getTeam()->getName(),
            'TeamTwo' => $tournamentMatch->getTeamTwo()->getTeam()->getName(),
        ]) ;
    }


    #[Route('/edit/{t_slug}/set-victory-poules/{id}' , name:'poules_set_victory')]
    public function setVictoryPoules(PouleMatch $pouleMatch, Request $request) {
        $form = $this->createForm(PouleMatchSetVictoryType::class,$pouleMatch) ;
        $form->handleRequest($request) ;
        if($form->isSubmitted() && $form->isValid()  ) {
            $em = $this->getDoctrine()->getManager() ;
            $pouleMatch->setStatus(true);
            if($pouleMatch->getFirstTeamWin() == true) {
                $pouleEquipeUne = $pouleMatch->getEquipeUne();
                $pouleEquipeUne->setNombreVictoire($pouleEquipeUne->getNombreVictoire() +1);
                $teamOne = $pouleEquipeUne->getTournamentTeam()->getTeam();
                $teamOne->setNbWin($teamOne->getNbWin() + 1);
                foreach ($teamOne->getUsers() as $user) {
                    $user->setNbWin($user->getNbWin()+1);
                    $em->persist($user);
                }
                $em->persist($teamOne);
            } else {
                $pouleEquipeDeux = $pouleMatch->getEquipeDeux();
                $pouleEquipeDeux->setNombreVictoire($pouleEquipeDeux->getNombreVictoire() +1);
                $teamTwo = $pouleEquipeDeux->getTournamentTeam()->getTeam();
                $teamTwo->setNbWin($teamTwo->getNbWin() + 1);
                foreach ($teamTwo->getUsers() as $user) {
                    $user->setNbWin($user->getNbWin()+1);
                    $em->persist($user);
                }
                $em->persist($teamTwo);
            }
            $em->persist($pouleMatch);
            $em->flush();
            $this->addFlash('success','Le vainqueur du match a été établi avec succès !');
            return $this->redirectToRoute('tournament_edit',[
                'slug' => $pouleMatch->getPoule()->getTournament()->getSlug()
            ]) ;
        }
        return $this->renderForm('tournament/editMatch.html.twig',[
            'match' => $pouleMatch,
            'TeamOne' => $pouleMatch->getEquipeUne()->getTournamentTeam()->getTeam()->getName(),
            'TeamTwo' => $pouleMatch->getEquipeDeux()->getTournamentTeam()->getTeam()->getName(),
            'form' => $form
        ]) ;
    }

    #[Route('/cancel/{slug}' , name:'tournament_cancel')]
    public function cancelTournament(Tournament $tournament, TournamentMatchRepository $matchRepository ,TournamentService $service)
    {
        if(!$tournament->getStatus()) {
            $em = $this->getDoctrine()->getManager();
            $count = 1 ;
            while($count < $tournament->getMaxTeamParticipant()) {
                $matches = $matchRepository->findBy(['name'=> $count, 'tournaments' => $tournament]) ;
                foreach ($matches as $match) {
                    if($match->getTeamOne() != null) {
                        $team = $match->getTeamOne();
                        $service->removePlayersOfTournamentTeam($team);
                        $team->removeTournamentMatch($match);
                        $em->remove($team);
                    }
                    if($match->getTeamTwo() != null) {
                        $team = $match->getTeamTwo();
                        $service->removePlayersOfTournamentTeam($team);
                        $team->removeTournamentMatch($match);
                        $em->remove($team);
                    }
                    $em->remove($match);
                    $em->flush();

                }
                $count++ ;
            }
            $em->remove($tournament);
            $em->flush();
            $this->addFlash('success','Le tournoi a bien été supprimé!');
        } else {
            $this->addFlash('waring','Le tournoi a déjà commencé. Tu ne peux pas l\'annuler');
        }
        return $this->redirectToRoute('tournament_dashboard') ;
    }

}
