<?php

namespace App\Controller\tournament\dashboard;

use App\Entity\Tournament;
use App\Entity\TournamentMatch;
use App\Form\TournamentType;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/create', name: 'tournament_new')]
    public function new(Request $request): Response
    {
        $tournament = new Tournament();
        $form = $this->createForm(TournamentType::class, $tournament);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->createMatchesForATournament($tournament->getMaxTeamParticipant(), $tournament, false) ;
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tournament);
            //$entityManager->flush();

            return $this->redirectToRoute('tournament_dashboard', [], Response::HTTP_CREATED);
        }

        return $this->renderForm('tournament/tournament_crud/new.html.twig', [
            'form' => $form,
        ]);
    }

    private function createMatchesForATournament(Int $max_team, Tournament $tournament,  Bool $isCallFromrecursive ,  $parents_matchs = null )
    {
        $em = $this->getDoctrine()->getManager();

        if($isCallFromrecursive == false) {
            $total_match = ( $max_team * 2 ) - 2 ;


            $finalMatch = new TournamentMatch('Finale',$tournament);
            $em->persist($finalMatch);

            $demi1 = new TournamentMatch('Demi-finale',$tournament, $finalMatch);
            $demi2 = new TournamentMatch('Demi-finale',$tournament,$finalMatch);
            $em->persist($demi1);
            $em->persist($demi2);

            $em->flush();
            if($total_match - 2 > 0)
                $this->createMatchesForATournament($total_match-2,$tournament,true ,array(
                    $demi1,
                    $demi2
                )) ;
        } else {
            $matches = [] ;

            foreach($parents_matchs as $match) {
               $matches[] = new TournamentMatch('',$tournament,$match) ;
               $matches[] = new TournamentMatch('',$tournament,$match) ;
            }
            foreach ($matches as $match){
                $em->persist($match);
            }
            $em->flush();

            if($max_team - count($matches)  > 0 )
                $this->createMatchesForATournament($max_team - (count($matches) ),$tournament,true,$matches) ;
        }

    }
}
