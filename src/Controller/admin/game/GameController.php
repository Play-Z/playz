<?php

namespace App\Controller\admin\game;

use App\Entity\Game;
use App\Form\AdminGameType;
use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/game')]
class GameController extends AbstractController
{
    #[Route('/', name: 'admin_game_index', methods: ['GET'])]
    public function index(GameRepository $gameRepository): Response
    {
        return $this->render('admin/game/index.html.twig', [
            'games' => $gameRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'game_new', methods: ['GET','POST'])]
    public function new(Request $request): Response
    {
        $game = new Game();
        $form = $this->createForm(AdminGameType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($game);
            $entityManager->flush();

            $this->addFlash('success', 'Votre jeu a bien été créer !');

            return $this->redirectToRoute('admin_game_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/game/new.html.twig', [
            'game' => $game,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}', name: 'admin_game_show', methods: ['GET'])]
    public function show(Game $game): Response
    {
        return $this->render('admin/game/show.html.twig', [
            'game' => $game,
        ]);
    }

    #[Route('/{slug}/edit', name: 'admin_game_edit', methods: ['GET','POST'])]
    public function edit(Request $request, Game $game): Response
    {
        $form = $this->createForm(AdminGameType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Le jeu a bien été modifier !');

            return $this->redirectToRoute('admin_game_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/game/edit.html.twig', [
            'game' => $game,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}', name: 'game_delete', methods: ['POST'])]
    public function delete(Request $request, Game $game): Response
    {
        if ($this->isCsrfTokenValid('delete'.$game->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($game);
            $entityManager->flush();
            $this->addFlash('success', 'Le jeu a bien été supprimer !');
        }

        return $this->redirectToRoute('admin_game_index', [], Response::HTTP_SEE_OTHER);
    }
}