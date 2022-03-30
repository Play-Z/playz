<?php

namespace App\Controller\play\dashboard;

use App\Entity\Tournament;
use App\Entity\TournamentTeam;
use App\Form\InscriptionType;
use App\Repository\TournamentRepository;
use App\Security\Voter\UserProfileVoter;
use App\Entity\User ;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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

    #[Route('/tournament/{id}', name:'play_tournament')]
    public function showInscription(Tournament $tournament): Response
    {

        return $this->render('play/tournament/show.html.twig',[
            'tournament' => $tournament,
            'inscription' => $tournament->getStartInscriptionAt() <= new \DateTime()
        ]) ;
    }

    #[ParamConverter('user', options: ['mapping' => ['user_slug' => 'slug']])]
    #[Route('/tournament/{id}/inscription/{user_slug}', name:'play_inscription_tournament')]
    #[IsGranted(UserProfileVoter::INSCRIPTION, 'user' )]
    public function doInscription(Tournament $tournament, User $user): Response
    {
        $tournament_team = new TournamentTeam() ;


        $form = $this->createForm(InscriptionType::class,$tournament_team,  [
            'team'=> $user->getTeam()->getUsers()
        ]) ;
        $form->handleRequest($form) ;
        if($form->isSubmitted() && $form->isValid()) {
            // TODO inscription of members
            // ! Check if user selection is less or equal to tournament max players
        }

        return $this->render('play/tournament/register.html.twig',[
            'form' => $form->createView(),
            'team' => $user->getTeam() ,
            'tournament' => $tournament
        ]) ;
    }

}
