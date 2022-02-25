<?php

namespace App\Controller\play\dashboard;

use App\Entity\Tournament;
use App\Repository\TournamentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlayDashboardController extends AbstractController
{
    #[Route('/', name: 'play_dashboard')]
    public function index(TournamentRepository $tournamentRepository): Response
    {

        return $this->render('play/play_dashboard/index.html.twig', [
            'tournaments'=>$tournamentRepository->findAll(),
        ]);
    }

    #[Route('/tournament/{tournament_id}', name:'play_tournament')]
    public function showInscription(Tournament $tournament): Response
    {
        return $this->render('play/tournament/show.html.twig',[
            'tournament' => $tournament
        ]) ;
    }
}
