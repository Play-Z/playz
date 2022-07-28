<?php

namespace App\Controller\admin\dashboard;

use App\Entity\User;
use App\Form\Tournament;
use App\Repository\TeamRepository;
use App\Repository\GameRepository;
use App\Repository\TournamentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends AbstractController
{
    #[Route('/', name: 'admin_dashboard')]
    public function index(GameRepository $gameRepository, TournamentRepository $tournamentRepository, TeamRepository $teamRepository): Response
    {
        $user = $this->getUser();
        // get all users from database
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        $tournaments = $tournamentRepository->findBy(['createdBy' => $user]);
        
        return $this->render('admin/admin_dashboard/index.html.twig', [
            'user' => $user,
            'users' => $users,
            'games' => $gameRepository->findAll(),
            'tournaments' => $tournaments,
            'teams' => $teamRepository->findAll()

        ]);
    }
}
