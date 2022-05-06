<?php


namespace App\Controller\admin\game;



use App\Entity\Game;
use App\Entity\GameType;
use App\Form\AdminGameType;
use App\Form\GameTypeAddType;
use App\Repository\GameRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/game-type')]
class GameTypeController extends AbstractController
{
    #[Route('/', name: 'gametype_dashboard', methods: ['GET'])]
    public function index(GameRepository $gameRepository) : Response {

        return $this->render('admin/game-type/index.html.twig',[
            'games' => $gameRepository->findAll()
        ]) ;
    }

    #[Route('/add/{slug}', name: 'gametype_add')]
    public function add(Game $game, Request $request)  {
        $type = new GameType() ;
        $form = $this->createForm(GameTypeAddType::class, $type);

        $form->handleRequest($request) ;
        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager() ;
            $game->addGameType($type) ;
            $em->persist($type);
            $em->persist($game);
            $em->flush();
            $this->addFlash('success','Mode de jeu ajouté avec success');
            return $this->redirectToRoute('gametype_dashboard') ;
        }
        return $this->renderForm('admin/game-type/add.html.twig',[
            'form' => $form ,
            'game' => $game
        ]) ;
    }

    #[ParamConverter('game', options: ['mapping' => ['game_slug' => 'slug']])]
    #[Route('/{game_slug}/edit/{slug}', name: 'gametype_edit')]
    public function edit(Game $game, GameType $gameType, Request $request)  {

        $form = $this->createForm(GameTypeAddType::class, $gameType);

        $form->handleRequest($request) ;
        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager() ;

            $em->persist($gameType);
            $em->persist($game);
            $em->flush();
            $this->addFlash('success','Mode de jeu a été edité avec success');
            return $this->redirectToRoute('gametype_dashboard') ;
        }
        return $this->renderForm('admin/game-type/edit.html.twig',[
            'form' => $form ,
            'type' => $gameType
        ]) ;
    }

    #[ParamConverter('game', options: ['mapping' => ['game_slug' => 'slug']])]
    #[Route('/{game_slug}/delete/{slug}', name: 'gametype_delete')]
    public function delete(Game $game, GameType $gameType, Request $request)  {
        $em = $this->getDoctrine()->getManager() ;
        $game->removeGameType($gameType) ;
        $em->remove($gameType);
        $em->persist($game);
        $em->flush();
        $this->addFlash('success','Mode de jeu a été supprimé avec success');
        return $this->redirectToRoute('gametype_dashboard') ;
    }


}