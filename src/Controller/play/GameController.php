<?php

namespace App\Controller\play;

use App\Entity\Game;
use App\Repository\GameRepository;
use App\Repository\TournamentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/game')]
class GameController extends AbstractController
{
    #[Route('/', name: 'game_index', methods: ['GET'])]
    public function index(GameRepository $gameRepository): Response
    {
        return $this->render('play/game/index.html.twig', [
            'games' => $gameRepository->findAll(),
        ]);
    }

    #[Route('/{slug}', name: 'game_show', methods: ['GET'])]
    public function show(Game $game, TournamentRepository $tournamentRepository): Response
    {
        $tournament = $tournamentRepository->findBy(['game' => $game]);

        return $this->render('play/game/show.html.twig', [
            'tournaments' => $tournament,
        ]);
    }
}
