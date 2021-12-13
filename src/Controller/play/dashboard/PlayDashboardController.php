<?php

namespace App\Controller\play\dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlayDashboardController extends AbstractController
{
    #[Route('/', name: 'play_dashboard')]
    public function index(): Response
    {
        return $this->render('play/play_dashboard/index.html.twig', [
            'controller_name' => 'PlayDashboardController',
        ]);
    }
}
