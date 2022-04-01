<?php

namespace App\Controller\tournament;

use App\Entity\Tournament;
use App\Entity\TournamentMatch;
use App\Form\EditTournamentType;
use App\Form\TournamentMatchSetVictoryType;
use App\Form\TournamentType;
use App\Repository\TournamentMatchRepository;
use App\Repository\TournamentRepository;
use App\Security\Voter\TournamentVoter;
use App\Service\TournamentService;
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

            $tournamentService->createMatchesForATournament($tournament->getMaxTeamParticipant(), $tournament, false);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tournament);
            //$entityManager->flush();

            return $this->redirectToRoute('tournament_dashboard', [], Response::HTTP_CREATED);
        }

        return $this->renderForm('tournament/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/edit/{slug}', name: 'tournament_edit')]
    public function edit(Request $request, Tournament $tournament,TournamentService $tournamentService): Response
    {
        if(!$tournament->getStatus()) {
            $form = $this->createForm(EditTournamentType::class, $tournament);

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager() ;
                $em->persist($tournament);
                $em->flush();
                $this->addFlash('success','Le tournoi a bien été modifié.');
                $this->redirectToRoute('tournament_dashboard') ;
            }
            return $this->renderForm('tournament/edit.html.twig', [
                'form' => $form,
                'tournament' => $tournament
            ]);
        } else {


            return $this->renderForm('tournament/edit.html.twig', [
                'matches' => $tournamentService->getMatchesOfEtape($tournament),
                'tournament' => $tournament
            ]);
        }


    }

    #[Route('/start/{slug}', name: 'tournament_start')]
    public function start(Request $request, Tournament $tournament, TournamentService $tournamentService): Response
    {
        if($tournament->getStartAt() <= new \DateTime() ) {
            if(count($tournament->getEquipes()) == $tournament->getMaxTeamParticipant()) {
                $tournamentService->startATournament($tournament);
                $this->addFlash('success','Le tournoi a bien été lancé');

            } else $this->addFlash('error','Le tournoi n`\'a pas pu être lancé. Il n\'y a pas assez d\'équipes inscrites ');
        } else $this->addFlash('error','Le tournoi n`\'a pas pu être lancé. La date de début n\'a pas été atteinte');
        return $this->redirectToRoute('tournament_dashboard') ;
    }

    #[Route('/edit/{t_slug}/set-victory/{id}' , name:'tournament_set_victory')]
    public function setVictory(TournamentMatch $tournamentMatch, Request $request) {
        $form = $this->createForm(TournamentMatchSetVictoryType::class,$tournamentMatch) ;
        $form->handleRequest($request) ;
        if($form->isSubmitted() && $form->isValid()  ) {
            $em = $this->getDoctrine()->getManager() ;
            $tournamentMatch->setStatus(true);
            $parentMatch =  $tournamentMatch->getMatchParent() ;
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
            $em->persist($tournamentMatch);
            $em->flush();

            $this->addFlash('success','Match victory has been set successfully !');
            return $this->redirectToRoute('tournament_edit',[
                'slug' => $tournamentMatch->getTournaments()->getSlug()
            ]) ;
        }
        return $this->renderForm('tournament/editMatch.html.twig',[
            'match' => $tournamentMatch,
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
