<?php

namespace App\Controller\tournament\dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TournamentDashboardController extends AbstractController
{
    #[Route('/', name: 'tournament_dashboard')]
    public function index(): Response
    {
        return $this->render('tournament/tournament_dashboard/index.html.twig', [
            'controller_name' => 'TournamentDashboardController',
        ]);
    }
}
