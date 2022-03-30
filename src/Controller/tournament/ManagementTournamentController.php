<?php

namespace App\Controller\tournament;

use App\Entity\Tournament;
use App\Form\EditTournamentType;
use App\Form\TournamentType;
use App\Repository\TournamentRepository;
use App\Security\Voter\TournamentVoter;
use App\Service\TournamentService;
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
    public function edit(Request $request, Tournament $tournament, TournamentService $tournamentService): Response
    {
        dd('TODO implement edit tournament');
        // TODO implement edit tournament
        // We have to see if we can just edit the front fields (description, name) or if we can modify the tournament generation
        $form = $this->createForm(EditTournamentType::class, $tournament);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

        }

        return $this->renderForm('tournament/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/start/{slug}', name: 'tournament_start')]
    public function start(Request $request, Tournament $tournament, TournamentService $tournamentService): Response
    {

        if($tournament->getStartAt() <= new \DateTime() ) {
            if(count($tournament->getEquipes()) == $tournament->getMaxTeamParticipant()) {
                $tournament->setStatus(true) ;
                // TODO Start tournament as a Service like TournamentService.startTournament($tournament) ;
                $em = $this->getDoctrine()->getManager() ;
                $em->persist($tournament);
                $em->flush();

                $this->addFlash('success','Le tournoi a bien été lancé');
            } else $this->addFlash('error','Le tournoi n`\'a pas pu être lancé. Il n\'y a pas assez d\'équipes inscrites ');
        } else $this->addFlash('error','Le tournoi n`\'a pas pu être lancé. La date de début n\'a pas été atteinte');
        return $this->redirectToRoute('tournament_dashboard') ;
    }

}
